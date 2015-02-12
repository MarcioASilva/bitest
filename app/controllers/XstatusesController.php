<?php

class XstatusesController extends Controller {

	/**
	 * Display a listing of xstatuses
	 *
	 * @return Response
	 */
	public function index()
	{
		$xstatuses = Xstatus::all();

		return View::make('xstatuses.index', compact('xstatuses'));
	}

	/**
	 * Show the form for creating a new status
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('xstatuses.create');
	}

	/**
	 * Store a newly created status in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Xstatus::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Xstatus::create($data);

		return Redirect::route('xstatuses.index');
	}

	/**
	 * Display the specified status.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$status = Xstatus::findOrFail($id);

		return View::make('xstatuses.show', compact('status'));
	}

	/**
	 * Show the form for editing the specified status.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$status = Xstatus::find($id);

		return View::make('xstatuses.edit', compact('status'));
	}

	/**
	 * Update the specified status in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$status = Xstatus::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Xstatus::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$status->update($data);

		return Redirect::route('xstatuses.index');
	}

	/**
	 * Remove the specified status from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Xstatus::destroy($id);

		return Redirect::route('xstatuses.index');
	}

}
