<?php

class PagesController extends Controller {

  private $dateTest;
  
  private $reportDate;
    
  private $exportedDate;

  private $report_start_date_range;

  private $report_end_date_range;
  
  private $previous_year;

  private $current_year;


  public function __construct()
  {
    $this->dateTest = Carbon::now();
    
    $this->reportDate               = $this->dateTest->subMonth()->lastOfMonth();
    
    $this->exportedDate             = Carbon::now();

    $this->report_start_date_range  = $this->dateTest->subYear()->startOfYear()->format('jS F Y');

    $this->report_end_date_range    = $this->dateTest->addMonth()->addYear()->addMonth()->lastOfMonth()->format('jS F Y');
    
    $this->previous_year            = $this->dateTest->subYear()->startOfYear()->format('Y');

    $this->current_year             = $this->dateTest->addYear();

  }

  //Provides values for Dropdown menu
  public function getSelectDropdown()
  {
    $selectDrodpdown = ExportedDate::orderBy('exported_date')->get();

    $trimmedDropdown = [];

    foreach($selectDrodpdown as $rows)
    {
      // $rows->exported_date = Carbon::createFromFormat('Y-m-d H:i:s', $rows->exported_date)->format('jS F Y');
      $rows->exported_date = Carbon::createFromTimeStamp(strtotime($rows->exported_date))->format('F Y');

      $trimmedDropdown[] = $rows->exported_date;
    }

    // includes get
    // all();
    // first();
    // find();

    // does not include get
    // orderBy()
    // where()

    return Response::json([
        'error'   => false,
        'records' => $trimmedDropdown
      ],
      200
    );
  }

  //Provides dates for start and end of reporting period
  // public function getDates($dropDownDate = 0)
  // {
    
  //   if(!$dropDownDate)
  //   {
  //     $dropDownDate           = $this->dateTest
  //   }

  //   $reportDate               = $dropDownDate->subMonth()->lastOfMonth()->format('jS F Y');
    
  //   $exportedDate             = $dropDownDate->addMonth()->format('F Y');//ExportedDate::orderBy('exported_date', 'desc')->first()->exported_date;

  //   $report_start_date_range  = $dropDownDate->subYear()->startOfYear()->format('jS F Y');

  //   $report_end_date_range    = $dropDownDate->addMonth()->addYear()->addMonth()->lastOfMonth()->format('jS F Y');
    
  //   $previous_year            = $dropDownDate->subYear()->startOfYear()->format('Y');

  //   $current_year            = $dropDownDate->addYear()->format('Y');

  //   return Response::json([
  //     'error'                     => false,
  //     'report_date'               => $reportDate,
  //     'exported_date'             => $exportedDate,//strtotime($exportedDate)
  //     'report_start_date_range'   => $report_start_date_range,
  //     'report_end_date_range'     => $report_end_date_range,
  //     'previous_year'             => $previous_year,
  //     'current_year'              => $current_year
  //     ],
  //     200
  //   );
  // }


  //Data for Cover page
  // bi.app/api/charts/cover-page
  public function getCoverPage($getDates = 0)
  {
    
    // $reportDate   = Carbon::createFromFormat('Y-m-d H:i:s', $exportedDate)
    //   ->subMonth()
    //   ->lastOfMonth()
    //   ->format('jS F Y');



    return Response::json([
        'error'   => false,
        'records' => [
          'report_date'   => $this->reportDate,
          'exported_date' => $this->exportedDate
        ]
      ],
      200
    );
  }


  // /api/charts/page1
  public function getVolumeSummaryTable()
  {

    $previous_year_records    = Record::where('id','=','1')->get(); //chain more

    return Response::json([
        'error'                 => false,
        'previous_year'         => $this->previous_year,
        'previous_year_records' => $previous_year_records->toArray(),
        'current_year'          => $this->$current_year

      ],
      200
    );
  }

  // public function getImportFile()
  // {

  //   $spreadsheet = Excel::load(public_path() . '/uploads/file_1k.csv')->get();

  //   foreach ($spreadsheet as $rows) {
  //     Dataset::create([
  //       'dataset'=> $rows->dataset
  //     ]);
  //   }

  //   var_dump('dataset entered');

  // }


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