<?php

class PagesController extends Controller {

  public function getVolumeSummaryTable(datasetId, from, to)
  {
    
    
    $volumeSummaryTable = Records::all();

    return Response::json(array(
        'error' => false,
        'peril' => $perils->toArray()
      ),
      200
    );
  }

}
