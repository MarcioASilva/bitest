<?php

class PagesController extends Controller {

  //Data for Cover page
  // /api/charts/cover-page
  public function getCoverPage()
  {

    $exportedDate = ExportedDate::orderBy('exported_date', 'desc')->first()->exported_date;
    $reportDate   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $exportedDate)
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
  public function getPage1() //VolumeSummaryTable
  {
    $volumeSummaryTable = Record::all();

    return Response::json([
        'error'   => false,
        'records' => $volumeSummaryTable->toArray()
      ],
      200
    );
  }

}
