<?php

class PagesController extends Controller {

  private $reportDate;
  private $exportedDate;
  private $firstDayOfLastYear;
  private $lastDayOfLastYear;
  private $lastDayOfLastMonth;
  private $firstDayOfThisYear;

  public function getSelectDropdown()
  {
    $selectDrodpdown = Report::orderBy('report_date', 'desc')->get();
    $trimmedDropdown = [];

    foreach($selectDrodpdown as $rows)
    {
      $rows->report_date = Carbon::createFromTimeStamp(strtotime($rows->report_date))->format('F Y');
      $trimmedDropdown[] = $rows->report_date;
    }

    return Response::json([
        'error'   => false,
        'records' => $trimmedDropdown
      ],
      200
    );
  }

  public function getCoverPage($reportId)
  {
    $this->setDates($reportId);

    return Response::json([
        'error'   => false,
        'records' => [
          'report_date'   => Carbon::createFromTimeStamp(strtotime($this->reportDate))->format('jS F Y'),
          'exported_date' => Carbon::createFromTimeStamp(strtotime($this->exportedDate))->format('jS F Y h:i A')
        ]
      ],
      200
    );
  }

  public function getPage2($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);

    // dd($this->lastDayOfLastYear);
    
    // Get the data
    $previousYearData = $this->groupAndCount($this->firstDayOfLastYear, $this->lastDayOfLastYear);
    $currentYearData  = $this->groupAndCount($this->firstDayOfThisYear, $this->lastDayOfLastMonth);

    // Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => $this->extractYear($this->lastDayOfLastYear),
        'previous_year_records' => $this->calculatePercentage($previousYearData),
        'previous_year_total'   => $this->yearTotal($this->firstDayOfLastYear, $this->lastDayOfLastYear),
        'current_year'          => $this->extractYear($this->firstDayOfThisYear),
        'current_year_records'  => $this->calculatePercentage($currentYearData),
        'current_year_total'    => $this->yearTotal($this->firstDayOfThisYear, $this->lastDayOfLastMonth),
      ],
      200
    );
  }

  public function getPage3($reportId)
  {
    $this->setDates($reportId);

    //Chosen dataset
    $datasetArray = Dataset::where('dataset', '=', 'AXA - Desktop')->first()->id;
    
    // Get the data
    $previousYearData = $this->groupByMonth($this->firstDayOfLastYear, $this->lastDayOfLastYear, $datasetArray);
    $currentYearData  = $this->groupByMonth($this->firstDayOfThisYear, $this->lastDayOfLastMonth, $datasetArray);

    // Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => $this->extractYear($this->lastDayOfLastYear),
        'series1'               => $this->$previousYearData,
        'current_year'          => $this->extractYear($this->firstDayOfThisYear),
        'series2'               => $this->$currentYearData,
      ],
      200
    );
  }

  public function getPage7($reportId)
  {
    $this->setDates($reportId);

    $fileStatus = FileStatus::where('file_statuses', '=', 'Closed')->first()->id;

    $series = getPieChart($this->firstDayOfThisYear, $this->lastDayOfLastMonth, $fileStatus);
  }

  /******************************************
  /*                                        */
  /*  Private functions                     */
  /*                                        */
  /******************************************/

  private function setDates($reportId)
  {
    $report = Report::find($reportId);

    $this->reportDate   = $report->report_date;
    $this->exportedDate = $report->records->first()->exportedDate->exported_date;

    $this->firstDayOfLastYear = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subYear()->startOfYear()->toDateTimeString();

    $this->lastDayOfLastYear = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subYear()->lastOfYear()->addDay()->toDateTimeString();

    $this->firstDayOfThisYear = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->startOfYear()->toDateTimeString();

    $this->lastDayOfLastMonth = Carbon::createFromTimeStamp(strtotime(($report->report_date)))
      ->subMonth()->lastOfMonth()->addDay()->toDateTimeString();
  }

  private function groupAndCount($start, $end)
  {
      // return Record::groupBy('dataset_id')
      //   ->whereBetween('date_received', [$start, $end])
      //   ->get([
      //     DB::raw('dataset_id'),
      //     DB::raw('COUNT(dataset_id) as total')
      //   ]);

    dd(DB::table('records')
      ->join('datasets', 'datasets.id', '=', 'records.dataset_id')
      ->select('slide2Friendly', 'slide2Sequence', 'records.id', 'slide2Sequence')
      ->where('date_received' => $start and 'date_received' <= $end)
      ->orderBy('slide2Sequence')
      ->groupBy('slide2Friendly' as 'total')
      ->get());
  }

  
  private function groupByMonth($start, $end, $datasetArray)
  {
    // Set some data placeholders
    $records = [];

    $query = Record::groupBy('month')
    ->whereBetween('date_received', [$start, $end])
    ->where(function($datasetArray)
    {
      foreach($datasetArray as $item)
      {
        $datasetArray->orWhere('dataset_id', '=', $item);
      }
    })
    ->get([
      DB::raw('MONTH(date_received) as month'),
      DB::raw('COUNT(dataset_id) as count')
    ]);

      // Build the final arrays
    foreach($query as $row)
    {

      $data = [];
      $data['month'] = Carbon::createFromTimeStamp(strtotime($row->month))->format('M');
      $data['count'] = number_format($row->count);
      
      $records[] = $data;
    }

    //Return everything
    return Response::json([
        'error'     => false,
        'api_data'  => $records,
      ],
      200
    );
  }

  private function calculatePercentage($data)
  {
    $sum        = 0;
    $returnData = [];

    foreach($data as $row)
    {
      $sum += $row->total;
    }

    foreach($data as $key => $row)
    {
      $returnData[$key]['dataset'] = $row->dataset->dataset;
      $returnData[$key]['count']   = number_format($row->total);
      $returnData[$key]['perc']    = $this->sortRounding(($row->total / $sum) * 100) . '%';
    }

    return $returnData;
  }

  private function sortRounding($float)
  {
    $arr = $float;

    $arr = str_split($arr);

    if ($arr[0]==0 && $arr[1]==0 && $arr[2]==0)
    {
      $float = round($float, 4);
    }

    if ($arr[0]==0 && $arr[1]==0)
    {
      $float = round($float, 3);
    }
    
    if ($arr[0]==0)
    {
      $float = round($float, 2);
    }
    else
     $float = round($float, 0); 

    return $float;
  }

  private function extractYear($date)
  {
    return Carbon::createFromTimeStamp(strtotime($date))->format('Y');
  }

  private function yearTotal($start, $end)
  {
    $count = Record::whereBetween('date_received', [$start, $end])->count();
    return number_format($count);
  }

}