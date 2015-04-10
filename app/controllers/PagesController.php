<?php

class PagesController extends Controller {

  private $reportDate;
  private $exportedDate;
  private $firstDayOfLastYear;
  private $lastDayOfLastYear;
  private $firstDayOfThisYear;
  private $lastDayOfLastPeriod;
  private $firstDayoflast12Monhts;
  private $lastDayoflast12Monhts;

  private $months = [
    1  => 'Jan',
    2  => 'Feb',
    3  => 'Mar',
    4  => 'Apr',
    5  => 'May',
    6  => 'Jun',
    7  => 'Jul',
    8  => 'Aug',
    9  => 'Sep',
    10 => 'Oct',
    11 => 'Nov',
    12 => 'Dec'
  ];

  public function getSelectDropdown()
  {
    $selectDrodpdown = Report::orderBy('report_date', 'desc')->get();
    $trimmedDropdown = [];

    foreach($selectDrodpdown as $key => $rows)
    {
      $rows->report_date = Carbon::createFromTimeStamp(strtotime($rows->report_date))->format('F Y');
      $trimmedDropdown[$key]['id']    = $rows->id;
      $trimmedDropdown[$key]['value'] = $rows->report_date;
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
          'report_date'   => Carbon::createFromTimeStamp(strtotime($this->reportDate))->->format('jS M Y'),
          'exported_date' => Carbon::createFromTimeStamp(strtotime($this->exportedDate))->format('jS M Y h:i A')
        ]
      ],
      200
    );
  }

  public function getPage2($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);
    
    // Get the data
    $previousYearData = $this->groupAndCountDatasets($this->firstDayOfLastYear, $this->lastDayOfLastYear);
    $currentYearData  = $this->groupAndCountDatasets($this->firstDayOfThisYear, $this->lastDayOfLastPeriod);


    // Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => $this->extractYear($this->firstDayOfLastYear),
        'previous_year_records' => $this->calculatePercentage($previousYearData),
        'previous_year_total'   => $this->yearTotal($this->firstDayOfLastYear, $this->lastDayOfLastYear),
        'current_year'          => $this->extractYear($this->firstDayOfThisYear),
        'current_year_records'  => $this->calculatePercentage($currentYearData),
        'current_year_total'    => $this->yearTotal($this->firstDayOfThisYear, $this->lastDayOfLastPeriod),
      ],
      200
    );
  }

  public function getPage3($reportId)
  {
    $this->setDates($reportId);

    $myObj = Dataset::whereIn('dataset', ['AXA - Desktop'])->get();

    // dataset
    $arrayOfDatasetIds = $this->eloquentObjToArrayOfIds($myObj);

    $previousYearDataParams = [
      'start'      => $this->firstDayOfLastYear,
      'end'        => $this->lastDayOfLastYear,
      'arrayOfIds' => $arrayOfDatasetIds
    ];

    $currentYearDataParams = [
      'start'      => $this->firstDayOfThisYear,
      'end'        => $this->lastDayOfLastPeriod,
      'arrayOfIds' => $arrayOfDatasetIds
    ];

    // Get the data
    $previousYearData = $this->groupByMonth($previousYearDataParams);
    $currentYearData  = $this->groupByMonth($currentYearDataParams);

    $series1 = $this->fillRecordsMonthGaps($previousYearData->toArray(), 12);
    $series2 = $this->fillRecordsMonthGaps($currentYearData->toArray(), $this->numberOfMonthsIntoThisYear());

    // Return everything
    return Response::json([
        'error'         => false,
        'previous_year' => $this->extractYear($this->firstDayOfLastYear),
        'series1'       => $series1,
        'current_year'  => $this->extractYear($this->firstDayOfThisYear),
        'series2'       => $series2,
      ],
      200
    );
  }

  public function getPage7($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);

    //Get the file_status
    $fileStatus = FileStatus::where('file_status', '=', 'Closed')->first()->id;

    $series = $this->groupAndCountReasons($this->firstDayOfThisYear, $this
      ->lastDayOfLastPeriod, $fileStatus);

    // Return everything
    return Response::json([
        'error'  => false,
        'records' => $this->calculatePercentage($series)
      ],
      200
    );
  }

  public function getPage8($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);

    $myObj = Dataset::whereIn('dataset', ['AXA - Desktop'])->get();

    // dataset
    $arrayOfDatasetIds = $this->eloquentObjToArrayOfIds($myObj);

    $series = $this->groupAndCountFileStatus($this->firstDayoflast12Monhts, $this
      ->lastDayoflast12Monhts, $arrayOfDatasetIds);

    // Return everything
    return Response::json([
        'error'   => false,
        'records' => $this->calculatePercentage($series)
      ],
      200
    );
  }


  /******************************************
  /*                                        */
  /*  Private functions                     */
  /*                                        */
  /******************************************/

  private function eloquentObjToArrayOfIds($obj)
  {
    $idsArray = [];

    foreach($obj as $datasetObj)
    {
      array_push($idsArray, $datasetObj->id);
    }

    return $idsArray;
  }

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

    $this->lastDayOfLastPeriod = Carbon::createFromTimeStamp(strtotime(($report->report_date)))
      ->subMonth()->lastOfMonth()->addDay()->toDateTimeString();

    $this->firstDayoflast12Monhts = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subYear()->startOfMonth()->subDay();

    $this->lastDayoflast12Monhts = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subMonth()->lastOfMonth()->addDay();
  }

  private function groupAndCountDatasets($start, $end)
  {
      return Record::groupBy('slide2Friendly')
        ->join('datasets', 'datasets.id', '=', 'records.dataset_id')
        ->whereBetween('date_received', [$start, $end])
        ->orderBy('slide2Sequence')
        ->get([
          DB::raw('dataset_id'),
          DB::raw('COUNT(dataset_id) as total'),
          DB::raw('slide2Sequence'),
          DB::raw('slide2Friendly as name')
        ]);
  }
  
  private function groupByMonth($array)
  {
    return Record::groupBy('month')
      ->whereBetween('date_received', [$array['start'], $array['end']])
      ->whereIn('dataset_id', $array['arrayOfIds'])
      ->get([
        DB::raw('MONTH(date_received) as month'),
        DB::raw('COUNT(dataset_id) as count')
      ]);
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
      $returnData[$key]['name']  = $row->name;
      $returnData[$key]['count'] = number_format($row->total);
      $returnData[$key]['perc']  = $this->sortRounding(($row->total / $sum) * 100) . '%';
    }

    return $returnData;
  }

  private function sortRounding($float)
  {
    $arr = str_split($float);

    // if($arr[0] == 0 && $arr[2] == 0 && $arr[3] == 0 && $arr[4] == 0)
    // {
    //   $float = round($float, 4);
    // }

    if($arr[0] == 0 && $arr[2] == 0 && $arr[3] == 0 && $arr[4] != 0)
    {
      $float = round($float, 3);
    }

    if($arr[0] == 0 && $arr[2] == 0 && $arr[3] != 0)
    {
      $float = round($float, 2);
    }
    
    if($arr[0] == 0 && $arr[2] != 0)
    {
      $float = round($float, 1);
    }

    elseif ($float >= 1)
    {
      $float = round($float, 0); 
    }

    return $float;
  }

  private function extractYear($date)
  {
    return Carbon::createFromTimeStamp(strtotime($date))->format('Y');
  }

  private function extractMonth($date)
  {
    return Carbon::createFromTimeStamp(strtotime($date))->format('m');
  }

  private function numberOfMonthsIntoThisYear()
  {
    return $this->extractMonth($this->reportDate) - 1;
  }

  private function yearTotal($start, $end)
  {
    $count = Record::whereBetween('date_received', [$start, $end])->count();
    return number_format($count);
  }

  private function groupAndCountReasons($start, $end, $fileStatus)
  {
    return Record::groupBy('work_not_proceeding_reason')
      ->select(DB::raw('work_not_proceeding_reason as name'), DB::raw('COUNT(records.id) as total'))
      ->join('reasons', 'reasons.id', '=', 'records.reason_id')
      ->whereBetween('date_received', [$start, $end])
      ->where('file_status_id', '=', $fileStatus)
      ->orderBy('total', 'desc')
      ->get();
  }

  private function groupAndCountFileStatus($start, $end, $arrayOfDatasetIds)
  {
    return Record::groupBy('file_status')
      ->select(DB::raw('file_status as name'), DB::raw('COUNT(records.id) as total'))
      ->join('file_statuses', 'file_statuses.id', '=', 'records.file_status_id')
      ->whereBetween('date_received', [$start, $end])
      ->whereIn('dataset_id', $arrayOfDatasetIds)
      ->orderBy('name', 'desc')
      ->get();
  }

  private function fillRecordsMonthGaps($records, $loops)
  {
    $offset = 0;
    $returnRecords = [];

    for($i = 0; $i < $loops; $i++)
    {
      if(isset($records[$i - $offset]['month']) && ($i + 1) == $records[$i - $offset]['month'])
      {
        $returnRecords[$i]['month'] = $this->months[$records[$i - $offset]['month']];
        $returnRecords[$i]['count'] = number_format($records[$i - $offset]['count']);
      }

      else
      {
        $offset++;

        $returnRecords[$i]['month'] = $this->months[$i + 1];
        $returnRecords[$i]['count'] = '0';
      }
    }

    return $returnRecords;
  }

  public function getBill()
  {
    echo 'a';
  }

}