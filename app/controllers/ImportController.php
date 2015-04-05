<?php

class ImportController extends Controller {

  private $slide2FriendlyLookup = [
    'AXA - Desktop' => [
      'AXA - Desktop'
    ],
    'BVS' => [
      'AXA - BVS'
    ],
    'ICL' => [
      'AXA - Imperial'
    ],
    'GAB' => [
      'AXA - GAB'
    ],
    'CL' => [
      'AXA - CL'
    ],
    'AXA - GAB WNS' => [
      'AXA - GAB WNS'
    ],
    'AXA - CL WNS'  => [
      'AXA - CL WNS'
    ],
    'WNS' => [
      'AXA - WNS',
      'AXA - Imperial WNS',
      'AXA - BVS WNS'
    ],
    'BRICS' => [
      'AXA - GAB BRICS'
    ],
    'ORIEL' => [
      'AXA - CL Oriel'
    ]
  ];

  private $slide2SequenceLookup = [
    1  => ['AXA - Desktop'],
    2  => ['AXA - BVS'],
    3  => ['AXA - Imperial'],
    4  => ['AXA - CL'],
    5  => ['AXA - GAB'],
    6  => ['AXA - CL WNS'],
    7  => ['AXA - GAB WNS'],
    8  => ['AXA - WNS'],
    9  => ['AXA - Imperial WNS'],
    10 => ['AXA - BVS WNS'],
    11 => ['AXA - GAB BRICS'],
    12 => ['AXA - CL Oriel']
  ];

  
  private $reasonsLookup = [
    'Abandoned'
      => ['Abandoned'],

    'Cash Settlement'
      => [
        'Cash Settlement',
        'Cash/Other Settlement',
        'Cash/Other Settlement - :STATUS_STRING'
      ],

    'Claim Under Policy Excess'
      => ['Claim Under Policy Excess'],

    'Other'
      => ['Other'],

    'Referred to Supplier'
      => ['Referred to Supplier'],

    'Repudiated'
      => ['Repudiated'],

    'Under Consideration'
      => ['Under Consideration'],

    'Withdrawn'
      => ['Withdrawn']
  ];

  private $datasetsReasonslookup = [
    'File Closed without a \'Work not Proceeding\' Reason' => [
      'AXA - Desktop',
      'AXA - BVS',
      'AXA - Imperial',
      'AXA - GAB',
      'AXA - CL'
    ],

    'Fulfillment' =>  [
      'AXA - GAB WNS',
      'AXA - CL WNS',
      'AXA - WNS',
      'AXA - Imperial WNS',
      'AXA - BVS WNS',
      'AXA - GAB BRICS',
      'AXA - CL Oriel'
    ]
  ];

  /**
   * Import csv file
   * @return json response
   */
  public function getIndex()
  {
    $path = public_path() . '/uploads/file_less_columns_full.csv';

    Excel::filter('chunk')->load($path)->chunk(50000, function($spreadsheet)
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
          'dataset'        => $row->dataset,
          'slide2Friendly' => $this->getArrayLookup($row->dataset, $this->slide2FriendlyLookup),
          'slide2Sequence' => $this->getArrayLookup($row->dataset, $this->slide2SequenceLookup)
        ];

        $file_status = [
          'file_status' => $row->file_status
        ];

        $peril = [
          'peril' => $row->peril
        ];

        $reason = [
          'work_not_proceeding_reason' => $this->getTwoArraysLookup($row->dataset, $row->work_not_proceeding_reason)
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
          'reason_id'                          => $this->getReasonIdByName($row->dataset, $row->work_not_proceeding_reason),
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

  private function getReasonIdByName($dataset, $reason)
  {
    $result = $this->getTwoArraysLookup($dataset, $reason);

    return Reason::where('work_not_proceeding_reason', '=', $result)
      ->first()
      ->id;
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

  private function nullably($model, $clause, $row)
  {
    if(isset($model::where($clause, '=', $row->$clause)->first()->id))
    {
      return $model::where($clause, '=', $row->$clause)->first()->id;
    }

    return null;
  }

  private function getArrayLookup($value, $lookupArray)
  {
    foreach($lookupArray as $key => $lookup)
    {
      if(in_array($value, $lookup))
      {
        return $key;
      }
    }

    return $value;
  }

  private function getTwoArraysLookup($dataset, $reason)
  {
    if(!$reason)
    {
      return $this->getArrayLookup($dataset, $this->datasetsReasonslookup);
    }

    return $this->getArrayLookup($reason, $this->reasonsLookup);
  }
// 
}