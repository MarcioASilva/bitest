<?php

class PerilsController extends \BaseController {

	/**
	 * Display a listing of perils
	 *
	 * @return Response
	 */
	public function index()
	{
		$perils = Peril::all();

		return View::make('perils.index', compact('perils'));
	}

	/**
	 * Show the form for creating a new peril
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('perils.create');
	}

	/**
	 * Store a newly created peril in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Peril::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Peril::create($data);

		return Redirect::route('perils.index');
	}

	/**
	 * Display the specified peril.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$peril = Peril::findOrFail($id);

		return View::make('perils.show', compact('peril'));
	}

	/**
	 * Show the form for editing the specified peril.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$peril = Peril::find($id);

		return View::make('perils.edit', compact('peril'));
	}

	/**
	 * Update the specified peril in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$peril = Peril::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Peril::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$peril->update($data);

		return Redirect::route('perils.index');
	}

	/**
	 * Remove the specified peril from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Peril::destroy($id);

		return Redirect::route('perils.index');
	}

}
