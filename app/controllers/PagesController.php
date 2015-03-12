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
      $rows->exported_date = Carbon::createFromTimeStamp(strtotime($rows->exported_date)->format('jS F Y'););

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
  // /api/charts/cover-page
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

    $exported_date = [
      'exported_date' => Carbon::now()->toDateTimeString()->format('jS F Y')
    ];

    $this->importRow($exported_date, 'ExportedDate');

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
        'reason' => $row->work_not_proceeding_reason
      ];

      $report_date = [
        'report_date' => Carbon::now()->toDateTimeString()
      ];

      $xstatuses = [
        'xstatuses' => $row->xactanalysis
      ];

      $records = [
        'date_delivered'                     => $row->date_delivered,
        'date_received'                      => $row->date_received,
        'date_returned'                      => $row->date_returned,
        'file_closed_date'                   => $row->file_closed_date,
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
        'report_id'                          => 1,
        'exported_date_id'                   => 1
      ];

      $this->importRow($dataset, 'Dataset');
      $this->importRow($file_status, 'FileStatus');
      $this->importRow($peril, 'Peril');
      $this->importRow($reason, 'Reason');
      $this->importRow($report_date, 'Report');
      $this->importRow($xstatuses, 'Xstatus');
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
      $model::create($data);
    }
  }

}