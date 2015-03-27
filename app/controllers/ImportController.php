<?php

class ImportController extends Controller {

  private $groupLookup = [
      1 => ['AXA - Desktop'],
      2 => ['AXA - BVS',
            'AXA - Imperial WNS'
            ]
      3 => ['AXA - GAB',
            'AXA - CL'
          ]
      4 => ['AXA - CL WNS',
            'AXA - GAB WNS',
            ''
          ]

AXA - CL Oriel

AXA - GAB BRICS
AXA - BVS WNS
AXA - Imperial


      ] 
    ];

  /**
   * Import csv file
   * @return json response
   */
  public function getIndex()
  {
    $path = public_path() . '/uploads/file_less_columns_full.csv';

    Excel::filter('chunk')->load($path)->chunk(500, function($spreadsheet)
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
          'dataset'       => $row->dataset,
          'group'         => $this->getGroup($row->dataset),
          // 'friendly_name' => 'ICL'
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
          'dataset_id'                         => Dataset::where('dataset', '=', $row->dataset)->first()->id,
          'xstatus_id'                         => Xstatus::where('xact_analysis', '=', $row->xactanalysis)->first()->id,
          'file_status_id'                     => FileStatus::where('file_status', '=', $row->file_status)->first()->id,
          'reason_id'                          => $this->nullably('Reason', 'work_not_proceeding_reason', $row),
          'peril_id'                           => $this->nullably('Peril', 'peril', $row),
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

  private function nullably($model, $clause, $row)
  {
    if(isset($model::where($clause, '=', $row->$clause)->first()->id))
    {
      return $model::where($clause, '=', $row->$clause)->first()->id;
    }

    return null;
  }

  private function getGroup($dataset)
  {
    foreach($this->groupLookup as $key => $lookup)
    {
      if(in_array($dataset, $lookup))
      {
        return $key;
      }
    }

    return 0;
  }

}