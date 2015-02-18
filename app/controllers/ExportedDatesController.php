<?php

class ExportedDatesController extends \BaseController {

	/**
	 * Display a listing of exporteddates
	 *
	 * @return Response
	 */
	public function index()
	{
		$exporteddates = Exporteddate::all();

		return View::make('exporteddates.index', compact('exporteddates'));
	}

	/**
	 * Show the form for creating a new exporteddate
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('exporteddates.create');
	}

	/**
	 * Store a newly created exporteddate in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Exporteddate::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Exporteddate::create($data);

		return Redirect::route('exporteddates.index');
	}

	/**
	 * Display the specified exporteddate.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$exporteddate = Exporteddate::findOrFail($id);

		return View::make('exporteddates.show', compact('exporteddate'));
	}

	/**
	 * Show the form for editing the specified exporteddate.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$exporteddate = Exporteddate::find($id);

		return View::make('exporteddates.edit', compact('exporteddate'));
	}

	/**
	 * Update the specified exporteddate in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$exporteddate = Exporteddate::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Exporteddate::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$exporteddate->update($data);

		return Redirect::route('exporteddates.index');
	}

	/**
	 * Remove the specified exporteddate from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Exporteddate::destroy($id);

		return Redirect::route('exporteddates.index');
	}

}
