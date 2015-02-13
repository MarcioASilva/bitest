<?php

class PerilsController extends Controller {

	/**
	 * Display a listing of perils
	 *
	 * @return Response
	 */
	public function index()
	{
		$perils = Peril::all();

		return Response::json(array(
				'error' => false,
				'peril' => $perils->toArray()
			),
			200
		);
	}

	// remove create

	/**
	 * Store a newly created peril in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Peril::$rules);

		if($validator->fails())
		{
			return Response::json(array(
					'error'   => 'true',
					'message' => $validator->messages()
				),
				400
			);
		}

		$peril = Peril::create($data);

		return Response::json(array(
				'error' => false,
				'peril' => $peril->toArray()
			),
			200
		);
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

		return Response::json(array(
				'error'  => false,
      	'perils' => $peril->toArray()
      ),
      200
		);
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

		if($validator->fails())
		{
			return Response::json(array(
					'error'   => 'true',
					'message' => $validator->messages()
				),
				400
			);
		}

		$peril->update($data);

		return Response::json(array(
				'error'  => false,
      	'perils' => $peril->toArray()
      ),
      200
		);
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

		return Response::json(array(
				'error'  => false,
      	'message' => 'Peril deleted successfully'
      ),
      200
		);
	}

}
