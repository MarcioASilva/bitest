<?php

class RecordsController extends Controller {

	/**
	 * Display a listing of records
	 *
	 * @return Response
	 */
	public function index()
	{
		$records = Record::all();

		return View::make('records.index', compact('records'));
	}

	/**
	 * Show the form for creating a new record
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('records.create');
	}

	/**
	 * Store a newly created record in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Record::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Record::create($data);

		return Redirect::route('records.index');
	}

	/**
	 * Display the specified record.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$record = Record::findOrFail($id);

		return View::make('records.show', compact('record'));
	}

	/**
	 * Show the form for editing the specified record.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$record = Record::find($id);

		return View::make('records.edit', compact('record'));
	}

	/**
	 * Update the specified record in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$record = Record::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Record::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$record->update($data);

		return Redirect::route('records.index');
	}

	/**
	 * Remove the specified record from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Record::destroy($id);

		return Redirect::route('records.index');
	}

}
