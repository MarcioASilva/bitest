<?php

class PagesController extends Controller {

  // /api/charts/volume-summary-table
  public function getVolumeSummaryTable()
  {
    $volumeSummaryTable = Record::all();



    //Record::;


    return Response::json(array(
        'error' => false,
        'records' => $volumeSummaryTable->toArray()
      ),
      200
    );
  }

}
