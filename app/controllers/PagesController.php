<?php

class PagesController extends Controller {


  public function getSelectDropdown()
  {
    // $selectDrodpdown = ExportedDate::orderBy('exported_date', 'desc')->exported_date;

    $selectDrodpdown = ExportedDate::orderBy('exported_date')->get();

    $trimmedDropdown = [];

    foreach($selectDrodpdown as $row)
    {
      // $row->exported_date = Carbon::createFromFormat('Y-m-d H:i:s', $row->exported_date)->format('jS F Y');
      $row->exported_date = Carbon::createFromTimeStamp(strtotime($row->exported_date))->format('F Y');

      $trimmedDropdown[] = $row;
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
    $volumeSummaryTable = Record::where('id','=','1')->('id','=','1')->get();
    //$volumeSummaryTable = Record::with('originalestimatevalue')->get();
    //find(1);

    return Response::json([
        'error'   => false,
        'records' => $volumeSummaryTable->toArray()
      ],
      200
    );
  }

}
