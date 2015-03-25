<?php

class PagesController extends Controller {

  private $reportDate;
  private $exportedDate;
  private $lastDayOfLastMonth;
  private $lastDayOfLastYear;
  private $firstDayOfThisYear;
  private $firstDayOfLastYear;


  public function getSelectDropdown()
  {
    $selectDrodpdown = Report::orderBy('report_date', 'desc')->get();
    $trimmedDropdown = [];

    foreach($selectDrodpdown as $rows)
    {
      // $rows->exported_date = Carbon::createFromFormat('Y-m-d H:i:s', $rows->exported_date)->format('jS F Y');
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
    $previousYearData = $this->totalsByName($this->firstDayOfLastYear, $this->lastDayOfLastYear);
    $currentYearData  = $this->totalsByName($this->firstDayOfThisYear, $this->lastDayOfLastMonth);

    // Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => $this->extractYear($this->lastDayOfLastYear),
        'previous_year_records' => $this->prepareArray($previousYearData),
        'previous_year_total'   => $this->duplicateTotal($this->firstDayOfLastYear, $this->lastDayOfLastYear),
        'current_year'          => $this->extractYear($this->firstDayOfThisYear),
        'current_year_records'  => $this->prepareArray($currentYearData),
        'current_year_total'    => $this->duplicateTotal($this->firstDayOfThisYear, $this->lastDayOfLastMonth),
      ],
      200
    );
  }

  public function getPage22($report)
  {
    $this->setDates($reportId);

    //Chosen dataset
    $datasetArray = Dataset::where('dataset', '=', 'Axa - Desktop')->first()->id;


    //Parameters are: (report_number, startOfRange, endOfRange, datasetArray)
    $series1 = lineChart(1, $this->firstDayOfLastYear, $this->lastDayOfLastYear,   $datasetArray);
    $series2 = lineChart(1, $this->firstDayOfThisYear, $this->$lastDayOfLastMonth, $datasetArray);
  }

  public function getPage7()
  {
    $fileStatus = FileStatus::where('file_statuses', '=', 'Closed')->first()->id;

    //Parameters are: (report_number, startOfRange, endOfRange, fileStatus)
    $series = getPieChart(1, $this->firstDayOfThisYear, $this->lastDayOfLastMonth, $fileStatus);
  }

  /**
   * Import csv file
   * @return json response
   */
  public function getImportFile()
  {
    Excel::filter('chunk')->load(public_path() . '/uploads/file_less_columns_Full.csv')->chunk(50000, function($spreadsheet)
    {
      $report_date = [
        'report_date' => Carbon::now()->toDateTimeString()
      ];

      $exported_date = [
        'exported_date' => Carbon::now()->toDateTimeString()
      ];

      $reportDateId   = $this->importRow($report_date, 'Report')->id;
      $exportedDateId = $this->importRow($exported_date, 'ExportedDate')->id;

      // Insert lookuptables
      foreach($spreadsheet as $key => $row)
      {
        $dataset = [
          'dataset' => $row->dataset
        ];

        $file_status = [
          'file_status' => $row->file_status
        ];

        $peril = [
          'peril' => $row->peril
        ];

        $reason = [
          'work_not_proceeding_reason' => $row->work_not_proceeding_reason
        ];

        $xstatuses = [
          'xact_analysis' => $row->xactanalysis
        ];

        $this->importRow($dataset, 'Dataset');
        $this->importRow($file_status, 'FileStatus');
        $this->importRow($peril, 'Peril');
        $this->importRow($reason, 'Reason');
        $this->importRow($xstatuses, 'Xstatus');
      }

      foreach($spreadsheet as $key => $row)
      {
        $records = [
          'date_received'                      => $this->transformDate($row->date_received),
          'date_delivered'                     => $this->transformDate($row->date_delivered),
          'date_returned'                      => $this->transformDate($row->date_returned),
          'file_closed_date'                   => $this->transformDate($row->file_closed_date),
          'total'                              => $row->total,
          'original_estimate_value'            => $row->original_estimate_value,
          'received_to_delivered_working_days' => $row->received_to_delivered_working_days,
          'received_to_returned_working_days'  => $row->received_to_returned_working_days,
          'received_to_closed_working_days'    => $row->received_to_closed_working_days,
          'dataset_id'                         => isset(Dataset::where('dataset', '=', $row->dataset)->first()->id) ? Dataset::where('dataset', '=', $row->dataset)->first()->id : null,
          'xstatus_id'                         => isset(Xstatus::where('xact_analysis', '=', $row->xactanalysis)->first()->id) ? Xstatus::where('xact_analysis', '=', $row->xactanalysis)->first()->id : null,
          'file_status_id'                     => isset(FileStatus::where('file_status', '=', $row->file_status)->first()->id) ? FileStatus::where('file_status', '=', $row->file_status)->first()->id : null,
          'reason_id'                          => isset(Reason::where('work_not_proceeding_reason', '=', $row->work_not_proceeding_reason)->first()->id) ? Reason::where('work_not_proceeding_reason', '=', $row->work_not_proceeding_reason)->first()->id : null,
          'peril_id'                           => isset(Peril::where('peril', '=', $row->peril)->first()->id) ? Peril::where('peril', '=', $row->peril)->first()->id : null,
          'report_id'                          => $reportDateId,
          'exported_date_id'                   => $exportedDateId
        ];

        $this->importRow($records, 'Record');
      }

    });

    return Response::json([
      'error'   => false,
      'message' => 'File imported successfully'
    ], 200);
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

    $this->lastDayOfLastMonth = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subMonth()->lastOfMonth()->toDateTimeString();

    $this->lastDayOfLastYear = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subYear()->lastOfYear()->toDateTimeString();

    $this->firstDayOfThisYear = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->startOfYear()->toDateTimeString();

    $this->firstDayOfLastYear = Carbon::createFromTimeStamp(strtotime($report->report_date))
      ->subYear()->startOfYear()->toDateTimeString();
  }

  private function importRow($data, $model)
  {
    $validator = Validator::make($data, $model::$rules);

    if(!$validator->fails())
    {
      return $model::create($data);
    }
  }

  private function transformDate($date)
  {
    if($date)
    {
      return Carbon::createFromFormat('d/m/y H:i', $date)->toDateTimeString(); 
    }

    return null;
  }

  private function lineChart($startOfRange, $endOfRange, $datasetArray)
  {
    // Set some data placeholders
    $records = [];
    
    $query = Record::groupBy('month')
      ->whereBetween('date_received', [$startOfRange, $endOfRange])
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
        'year'      => Carbon::createFromTimeStamp(strtotime($this->lastDayOfLastYear))->format('Y'),
        'api_data'  => $records,
      ],
      200
    );
  }

  private function groupCountPercentage($groupByField)
  {
    // Set some data placeholders
    $recordsPart1 = [];

    // Part 1
    $recordsPart1 = Record::groupBy($groupByField)
      ->whereBetween('date_received', [$this->firstDayOfThisYear, $this->lastDayOfLastMonth])
      ->get([
        DB::raw('file_status'),
        DB::raw('COUNT(dataset_id) as count')
      ]);
  }

  private function totalsByName($start, $end)
  {
    return Record::select('*', DB::raw('count(*) as total'))
      ->whereBetween('date_received', [$start, $end])
      ->groupBy('dataset_id')
      ->get();
  }

  private function duplicateTotal($start, $end)
  {
    $count = Record::whereBetween('date_received', [$start, $end])->count();
    return number_format($count);
  }

  private function prepareArray($data)
  {
    
    $sum = 0;
    foreach ($data as $value) {
      $sum = $sum + $value->total;
    }

    foreach($data as $row)
    {
      $data['dataset'] = $row->dataset->dataset;
      $data['count']   = $row->total;
      $data['perc']    = round((float)($row->total / $sum) * 100) . '%';
    }

    return $data;

  }

  private function extractYear($date)
  {
    return Carbon::createFromTimeStamp(strtotime($date))->format('Y');
  }















  //
  // To be deleted
  //
  private function pieChart($fileStatus)
  {
    // Set some data placeholders
    $recordsPart1 = [];
    $recordsPart2 = [];

     // Part 1
    $recordsPart1 = Record::groupBy('file_status')
      ->whereBetween('date_received', [$this->firstDayOfThisYear, $this->lastDayOfLastMonth])
      ->get([
        DB::raw('file_status'),
        DB::raw('COUNT(dataset_id) as count')
      ]);

    // Part 2
    $recordsPart2 = Record::groupBy('work_not_proceeding_reason')
      ->whereBetween('date_received', [$this->firstDayOfThisYear, $this->lastDayOfLastMonth])
      ->where('file_status_id', $fileStatus)
      ->get([
        DB::raw('work_not_proceeding_reason as reason'),
        DB::raw('COUNT(dataset_id) as count')
      ]);

    return [
      'year'         => Carbon::createFromTimeStamp(strtotime($this->startOfRange))->format('Y'),
      'recordsPart1' => number_format($recordsPart1),
      'recordsPart2' => number_format($recordsPart2)
    ];
  }

  private function oldLineChart()
  {
    // Set some data placeholders
    $previous_year_records = [];
    
    $previousYearData = Record::groupBy('month')
      ->whereBetween('date_received', [$this->firstDayOfLastYear, $this->lastDayOfLastYear])
      ->get([
        DB::raw('MONTH(date_received) as month'),
        DB::raw('COUNT(dataset_id) as count')
      ]);

    // Build the final arrays
    foreach($previousYearData as $row)
    {
      $data = [];

      $data['month'] = Carbon::createFromFormat('m', $row->month)->format('M');
      $data['count'] = number_format($row->count);

      $previous_year_records[] = $data;
    }

    //Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => Carbon::createFromTimeStamp(strtotime($this->lastDayOfLastYear))->format('Y'),
        'previous_year_records' => $previous_year_records,
        'current_year'          => Carbon::createFromTimeStamp(strtotime($this->firstDayOfThisYear))->format('Y'),
        'current_year_records'  => $current_year_records,
      ],
      200
    );
  }

}