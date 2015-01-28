<?php

class StatusesController extends \BaseController {

	/**
	 * Display a listing of statuses
	 *
	 * @return Response
	 */
	public function index()
	{
		$statuses = Status::all();

		return View::make('statuses.index', compact('statuses'));
	}

	/**
	 * Show the form for creating a new status
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('statuses.create');
	}

	/**
	 * Store a newly created status in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Status::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Status::create($data);

		return Redirect::route('statuses.index');
	}

	/**
	 * Display the specified status.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$status = Status::findOrFail($id);

		return View::make('statuses.show', compact('status'));
	}

	/**
	 * Show the form for editing the specified status.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$status = Status::find($id);

		return View::make('statuses.edit', compact('status'));
	}

	/**
	 * Update the specified status in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$status = Status::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Status::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$status->update($data);

		return Redirect::route('statuses.index');
	}

	/**
	 * Remove the specified status from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Status::destroy($id);

		return Redirect::route('statuses.index');
	}

}
