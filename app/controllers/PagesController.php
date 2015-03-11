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
      $rows->exported_date = Carbon::createFromTimeStamp(strtotime($rows->exported_date))->format('F Y');

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

    //Imports spreadsheet into $spreadsheet
    $spreadsheet = Excel::load(public_path() . '/uploads/file_1k.csv')->get();
    
    $spreadsheetDataset[]='';
    $counter1=0;

    //puts dataset from $rows in to $spreadsheetDataset array
    foreach ($spreadsheet as $rows) {
      $spreadsheetDataset[$counter1] = $rows ->dataset.'<br>';
      $counter1++;
      // $rows = $rows ->toArray();
      // print $rows.'<br>';
      // echo $rows;
    }

    //remove duplicates Aarays
    $spreadsheetDataset = array_unique($spreadsheetDataset);
    // print_r($spreadsheetDataset);
    
    echo 'Resultes from Spreadsheet: '.'<br>';
    foreach ($spreadsheetDataset as $key => $value) {
      echo $value;
    }



    //Dataset from DB
    $DB = Dataset::all();
    
    $DBDataset[]='';
    $counter2=0;

    //puts dataset from $rows in to $spreadsheetDataset array
    foreach ($DB as $rows) {
      $DBDataset[$counter2] = $rows ->dataset.'<br>';
      $counter2++;
      // $rows = $rows ->toArray();
      // print $rows.'<br>';
      // echo $rows;
    }

    //remove duplicates Aarays
    $DBDataset = array_unique($DBDataset);

    //echo $DB;

    echo '<br>'.'Resultes from DB: '.'<br>';
    foreach ($DBDataset as $key => $value) {
      echo $value;
    }
    // var_dump($DB);

    //Compare results from DB x Spreadsheet

    //call comparing function 
  }

  public function CompareArrays(array1, array2)
  {
    foreach ($variable as $key => $value) {
      # code...
    }
  }
}