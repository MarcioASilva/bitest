<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::group(array('prefix' => 'api'), function()
{
  Route::resource('perils', 'PerilsController');
  Route::resource('reasons', 'ReasonsController');
  Route::resource('records', 'RecordsController');
  Route::resource('reports', 'ReportsController');
  Route::resource('datasets', 'DatasetsController');
  Route::resource('xstatuses', 'XstatusesController');
  Route::resource('filestatuses', 'FileStatusesController');
});
