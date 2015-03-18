<?php

class PagesController extends Controller {

  private $reportDate;
  private $exportedDate;
  private $lastDayOfLastMonth;
  private $lastDayOfLastYear;
  private $firstDayOfThisYear;
  private $firstDayOfLastYear;


  public function setDates($reportId)
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

  //Provides values for Dropdown menu
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


  //Data for Cover page
  // bi.app/api/charts/cover-page
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

  // /api/charts/page1
  public function getPage1($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);

    // Set some data placeholders
    $previous_year_records = [];
    $current_year_records  = [];

    // Get the totals
    $previous_year_total = number_format(Record::whereBetween(
      'date_received',
      [$this->firstDayOfLastYear, $this->lastDayOfLastYear])->count());

    // dd($previous_year_total);
    $current_year_total = Record::whereBetween(
      'date_received',
      [$this->firstDayOfThisYear, $this->lastDayOfLastMonth])->count();

    // Get the data
    $previousYearData = Record::select('*', DB::raw('count(*) as total'))
      ->whereBetween('date_received', [$this->firstDayOfLastYear, $this->lastDayOfLastYear])
      ->groupBy('dataset_id')
      ->get();

    $currentYearData = Record::select('*', DB::raw('count(*) as total'))
      ->whereBetween('date_received', [$this->firstDayOfThisYear, $this->lastDayOfLastMonth])
      ->groupBy('dataset_id')
      ->get();

    // Build the final arrays
    foreach($previousYearData as $row)
    {
      $data = [];

      $data['dataset'] = $row->dataset->dataset;
      $data['count']   = $row->total;
      $data['perc']    = round((float)($row->total / $previous_year_total) * 100) . '%';

      $previous_year_records[] = $data;
    }

    foreach($currentYearData as $row)
    {
      $data = [];

      $data['dataset'] = $row->dataset->dataset;
      $data['count']   = number_format($row->total);
      $data['perc']    = round((float)($row->total / $current_year_total) * 100) . '%';

      $current_year_records[] = $data;
    }

    // Return everything
    return Response::json([
        'error'                 => false,
        'previous_year'         => Carbon::createFromTimeStamp(strtotime($this->lastDayOfLastYear))->format('Y'),
        'previous_year_records' => $previous_year_records,
        'previous_year_total'   => $previous_year_total,
        'current_year'          => Carbon::createFromTimeStamp(strtotime($this->firstDayOfThisYear))->format('Y'),
        'current_year_records'  => $current_year_records,
        'current_year_total'    => $current_year_total,
      ],
      200
    );
  }

    // /api/charts/page1
  public function getPage2($reportId)
  {
    // Set the dates for late usage
    $this->setDates($reportId);

    // Set some data placeholders
    $previous_year_records = [];
    $current_year_records  = [];
    
    // Get the data
    $previousYearData = Record::select('*', DB::raw('count(*) as total'))
      ->whereBetween('date_received', [$this->firstDayOfLastYear, $this->lastDayOfLastYear])
      ->groupBy(DB::raw('MONTH(date_received)'))
      ->get();

    // Build the final arrays
    foreach($previousYearData as $row)
    {
      $data = [];
      $data['date_received'] = Carbon::createFromTimeStamp(strtotime($row->date_received))->format('M');
      $data['count']         = $row->total;
      $previous_year_records[] = $data;
    }

    // Record::select(DB::raw('*'))
    //   ->whereRaw('asdfasdf > asdfasdf')
    //   ->groupBy(DB::raw('asdfadsf'))
    //   ->get();

    dd($previous_year_records);

    $currentYearData = Record::select('*', DB::raw('count(*) as total'))
      ->whereBetween('date_received', [$this->firstDayOfThisYear, $this->lastDayOfLastMonth])
      ->groupBy('date_received')
      ->get();

    foreach($currentYearData as $row)
    {
      $data = [];

      $data['dataset'] = $row->dataset->dataset;
      $data['count']   = $row->total;

      $current_year_records[] = $data;
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

  public function getImportFile()
  {
    $spreadsheet = Excel::load(public_path() . '/uploads/file_1k.csv')->get();

    $report_date = [
      'report_date' => Carbon::now()->toDateTimeString()
    ];

    $exported_date = [
      'exported_date' => Carbon::now()->toDateTimeString()
    ];

    $reportDateId   = $this->importRow($report_date, 'Report')->id;
    $exportedDateId = $this->importRow($exported_date, 'ExportedDate')->id;

    // Insert lookuptables
    foreach($spreadsheet as $row)
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

    foreach($spreadsheet as $row)
    {
      $records = [
        'date_delivered'                     => $this->transformDate($row->date_delivered),
        'date_received'                      => $this->transformDate($row->date_received),
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

    return Response::json([
        'error'   => false,
        'message' => 'File imported successfully'
      ],
      200
    );

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

}