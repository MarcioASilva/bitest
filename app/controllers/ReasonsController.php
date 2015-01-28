<?php

class ReasonsController extends \BaseController {

	/**
	 * Display a listing of reasons
	 *
	 * @return Response
	 */
	public function index()
	{
		$reasons = Reason::all();

		return View::make('reasons.index', compact('reasons'));
	}

	/**
	 * Show the form for creating a new reason
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('reasons.create');
	}

	/**
	 * Store a newly created reason in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Reason::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Reason::create($data);

		return Redirect::route('reasons.index');
	}

	/**
	 * Display the specified reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$reason = Reason::findOrFail($id);

		return View::make('reasons.show', compact('reason'));
	}

	/**
	 * Show the form for editing the specified reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$reason = Reason::find($id);

		return View::make('reasons.edit', compact('reason'));
	}

	/**
	 * Update the specified reason in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$reason = Reason::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Reason::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$reason->update($data);

		return Redirect::route('reasons.index');
	}

	/**
	 * Remove the specified reason from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Reason::destroy($id);

		return Redirect::route('reasons.index');
	}

}
