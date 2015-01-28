<?php

class FilesController extends \BaseController {

	/**
	 * Display a listing of files
	 *
	 * @return Response
	 */
	public function index()
	{
		$files = File::all();

		return View::make('files.index', compact('files'));
	}

	/**
	 * Show the form for creating a new file
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('files.create');
	}

	/**
	 * Store a newly created file in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), File::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		File::create($data);

		return Redirect::route('files.index');
	}

	/**
	 * Display the specified file.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$file = File::findOrFail($id);

		return View::make('files.show', compact('file'));
	}

	/**
	 * Show the form for editing the specified file.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$file = File::find($id);

		return View::make('files.edit', compact('file'));
	}

	/**
	 * Update the specified file in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$file = File::findOrFail($id);

		$validator = Validator::make($data = Input::all(), File::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$file->update($data);

		return Redirect::route('files.index');
	}

	/**
	 * Remove the specified file from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		File::destroy($id);

		return Redirect::route('files.index');
	}

}
