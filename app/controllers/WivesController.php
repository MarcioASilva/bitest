<?php

class WivesController extends Controller {

	/**
	 * Display a listing of wives
	 *
	 * @return Response
	 */
	public function index()
	{
		$wives = Wife::all();

		return Response::json($wives);
	}

	// *
	//  * Show the form for creating a new wife
	//  *
	//  * @return Response
	 
	// public function create()
	// {
	// 	return View::make('wives.create');
	// }

	/**
	 * Store a newly created wife in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Wife::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Wife::create($data);

		return Redirect::route('wives.index');
	}

	/**
	 * Display the specified wife.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	// public function show($id)
	// {
	// 	$wife = Wife::findOrFail($id);

	// 	return View::make('wives.show', compact('wife'));
	// }

	/**
	 * Show the form for editing the specified wife.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	// public function edit($id)
	// {
	// 	$wife = Wife::find($id);

	// 	return View::make('wives.edit', compact('wife'));
	// }

	/**
	 * Update the specified wife in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$wife = Wife::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Wife::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$wife->update($data);

		return Redirect::route('wives.index');
	}

	/**
	 * Remove the specified wife from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Wife::destroy($id);

		return Redirect::route('wives.index');
	}

}
