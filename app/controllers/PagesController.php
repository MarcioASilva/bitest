<?php

class PagesController extends Controller {


  public function getSelectDropdown()
  {
    // $selectDrodpdown = ExportedDate::orderBy('exported_date', 'desc')->exported_date;

    $selectDrodpdown = ExportedDate::orderBy('exported_date')->get();

    $trimmedDropdown = [];

    foreach($selectDrodpdown as $rows)
    {
      // $rows->exported_date = Carbon::createFromFormat('Y-m-d H:i:s', $rows->exported_date)->format('jS F Y');
      $rows->exported_date = Carbon::createFromTimeStamp(strtotime($rows->exported_date));

      $trimmedDropdown[] = $rows;
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

  //Data for Cover page
  // bi.app/api/charts/cover-page
  public function getCoverPage()
  {

    $exportedDate = ExportedDate::orderBy('exported_date', 'desc')->first()->exported_date;
    $reportDate   = Carbon::createFromFormat('Y-m-d H:i:s', $exportedDate)
      ->subMonth()
      ->lastOfMonth()
      ->format('jS F Y');

    return Response::json([
        'error'   => false,
        'records' => [
          'exported_date' => strtotime($exportedDate),
          'report_date'   => $reportDate
        ]
      ],
      200
    );
  }

  // /api/charts/page1
  public function getVolumeSummaryTable()
  {
    $volumeSummaryTable = Record::where('id','=','1')->get(); //chain more

    return Response::json([
        'error'   => false,
        // 'records' => $volumeSummaryTable->toArray()
        'records' => $volumeSummaryTable->toArray()
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
        'file_id'                            => isset(FileStatus::where('file_status', '=', $row->file_status)->first()->id) ? FileStatus::where('file_status', '=', $row->file_status)->first()->id : null,
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