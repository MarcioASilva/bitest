<?php

class HusbandsController extends Controller {

	/**
	 * Display a listing of husbands
	 *
	 * @return Response
	 */
	public function index()
	{
		$husbands = Husband::all();

		return Response::json($husbands);
	}

	/**
	 * Store a newly created husband in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Husband::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Husband::create($data);

		return Redirect::route('husbands.index');
	}

	/**
	 * Update the specified husband in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$husband = Husband::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Husband::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$husband->update($data);

		return Redirect::route('husbands.index');
	}

	/**
	 * Remove the specified husband from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Husband::destroy($id);

		return Redirect::route('husbands.index');
	}

}
