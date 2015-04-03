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

  public function getSelectDropdown()
  {
    $selectDrodpdown = Report::orderBy('report_date', 'desc')->get();
    $trimmedDropdown = [];
// 
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

    $myObj = Dataset::whereIn('dataset', ['AXA - Desktop', 'aa'])->get();

    // dataset
    $arrayOfDatasetIds = $this->eloquentObjToArrayOfIds($myObj);
    
    // Get the data
    $previousYearData = $this->groupByMonth($this->firstDayOfLastYear, $this->lastDayOfLastYear, $arrayOfDatasetIds);
    $currentYearData  = $this->groupByMonth($this->firstDayOfThisYear, $this->lastDayOfLastPeriod, $arrayOfDatasetIds);

    // Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => $this->extractYear($this->firstDayOfLastYear),
        'series1'               => $this->numberToMonth($previousYearData),
        'current_year'          => $this->extractYear($this->firstDayOfThisYear),
        'series2'               => $this->numberToMonth($currentYearData),
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

    // dd($series);
    // Return everything
    return Response::json([
        'error'                 => false,
        'current_year_records'  => $this->calculatePercentage($series),
      ],
      200
    );
  }


  public function getPage8($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);

    //Get the file_status
    $fileStatus = FileStatus::where('file_status', '=', 'Closed')->first()->id;

    $series = $this->groupAndCountReasons($this->firstDayOfThisYear, $this
      ->lastDayOfLastPeriod, $fileStatus);

    // Return everything
    return Response::json([
        'error'                 => false,
        'current_year_records'  => $this->calculatePercentage($series),
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
      ->subYear()->subMonth()->startOfMonth();

    $this->lastDayoflast12Monhts = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subMonth()->lastOfMonth();
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
          DB::raw('slide2Friendly')
        ]);
  }
  
  private function groupByMonth($start, $end, $arrayOfIds)
  {
    return Record::groupBy('month')
      ->whereBetween('date_received', [$start, $end])
      ->whereIn('dataset_id', $arrayOfIds)
      ->get([
        DB::raw('MONTH(date_received) as month'),
        DB::raw('COUNT(dataset_id) as count'),
        DB::raw('date_received as fulldate')
      ]);
  }

  private function calculatePercentage($data)
  {
    $sum        = 0;
    $returnData = [];

    foreach($data as $row)
    {
      // dd($row);
      $sum += $row->total;
    }

    foreach($data as $key => $row)
    {
      $returnData[$key]['name']   = $row->slide2Friendly;
      $returnData[$key]['count']  = number_format($row->total);
      $returnData[$key]['perc']   = $this->sortRounding(($row->total / $sum) * 100) . '%';
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

  private function numberToMonth($data)
  {
    foreach($data as $key => $row)
    {
      $returnData[$key]['month'] = Carbon::createFromTimeStamp(strtotime($row
        ->fulldate))->format('M');
      
      $returnData[$key]['count'] = number_format($row->count);
    }

    return $returnData;
  }

  private function groupAndCountReasons($start, $end, $fileStatus)
  {
    // return
     dd(DB::table('records')
      ->select('count(id) as count, reason_id')
      ->groupBy('reason_id')
        ->join('reasons', 'reasons.id', '=', 'records.reasons_id')
        ->whereBetween('date_received', [$start, $end])
        ->orderBy('date_received', 'desc')
        ->get(
        //   [
        //   DB::raw('id'),
        //   // DB::raw('COUNT(id) as total')
        //   // DB::raw('COUNT(dataset_id) as total')
        // ]
        ));

    // return DB::table('records')
    //   ->join('reasons', 'reasons.id', '=', 'records.reason_id')
    //   // ->select('work_not_proceeding_reason', 'records.id')
    //   ->whereBetween('date_received', [$start, $end])
    //   ->where('file_status_id', '=', $fileStatus)
    //   ->groupBy('work_not_proceeding_reason')
    //   ->orderBy('total', 'desc')
    //   ->get([
    //     DB::raw('work_not_proceeding_reason'),
    //     DB::raw('COUNT(work_not_proceeding_reason) as total'),
    //     // DB::raw('reasons')
      // ]);

    // DB::table('users')
    //         ->join('contacts', 'users.id', '=', 'contacts.user_id')
    //         ->join('orders', 'users.id', '=', 'orders.user_id')
    //         ->select('users.id', 'contacts.phone', 'orders.price')
    //         ->get();
  }

}