<?php

class DatasetsController extends \BaseController {

	/**
	 * Display a listing of datasets
	 *
	 * @return Response
	 */
	public function index()
	{
		$datasets = Dataset::all();

		return View::make('datasets.index', compact('datasets'));
	}

	/**
	 * Show the form for creating a new dataset
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('datasets.create');
	}

	/**
	 * Store a newly created dataset in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Dataset::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Dataset::create($data);

		return Redirect::route('datasets.index');
	}

	/**
	 * Display the specified dataset.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$dataset = Dataset::findOrFail($id);

		return View::make('datasets.show', compact('dataset'));
	}

	/**
	 * Show the form for editing the specified dataset.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$dataset = Dataset::find($id);

		return View::make('datasets.edit', compact('dataset'));
	}

	/**
	 * Update the specified dataset in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$dataset = Dataset::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Dataset::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$dataset->update($data);

		return Redirect::route('datasets.index');
	}

	/**
	 * Remove the specified dataset from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Dataset::destroy($id);

		return Redirect::route('datasets.index');
	}

}
