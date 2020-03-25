<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminApiController extends Controller {

	// Path where game definition uploads should be stored (relative to /storage/app.)
	protected const DEFINITIONS_PATH = 'definitions';

	// What error message to display when a non-existent game ID is passed into
	// a controller action.
	protected const GAME_404_MESSAGE = 'Game not found';

	// What error message to display when a non-existent definition ID is passed
	// into a controller action.
	protected const DEFINITION_404_MESSAGE = 'Game definition not found';

	// What error message to display when attempting to create a game using a
	// definition that hasn't actually been uploaded yet.
	protected const DEFINITION_MISSING_FILE = "An entry for the definition exists, but a file hasn't been uploaded.";

	// If we're in production mode and a query error occurs, this is the
	// generic message we should return to the user.
	protected const GENERIC_500_MESSAGE = 'An error occured. Please try your query again in a few minutes.';

	// Validation rule => error message mapping for game definition file uploads
	protected const DEFINITION_UPLOAD_ERRORS = [
		'mimetypes' => 'Game definition must be valid XML',
		'required'  => 'Missing game definition file',
		'file'      => 'Missing game definition file'
	];

	// Validation rule => error message mapping for game creation input parameters
	protected const CREATE_GAME_INPUT_ERRORS = [
		'regex'     => 'Please input a valid game definition ID',
		'required'  => 'Missing one or more required parameters',
	];

	// Instance of \Illuminate\Http\Request
	protected $request;

	/*************************************************************************/

	/**
	 * Utility method that "throws" an exception as JSON to the client. This
	 * makes more sense for an API, which expects a JSON result.
	 *
	 * @param \Illuminate\Database\QueryException $e Exception that was thrown
	 * @return \Illuminate\Http\JsonResponse
	*/
	protected function throwQueryExceptionAsJson(
		 \Illuminate\Database\QueryException $e
	): \Illuminate\Http\JsonResponse {

		if ('production' == config('app.env')) {

			return response()->json([
				'error' => self::GENERIC_500_MESSAGE
			], 500);
		}

		else {
			return response()->json([
				'error'    => $e->getMessage(),
				'sql'      => $e->getSql(),
				'bindings' => $e->getBindings(),
				'code'     => $e->getCode(),
				'file'     => $e->getFile(),
				'line'     => $e->getLine(),
				'trace'    => $e->getTrace()
			], 500);
		}
	}

	/*************************************************************************/

	public function __construct(
		\Illuminate\Http\Request $request
	) {
		$this->request = $request;
		$this->trogdord = new \Trogdord(
			config('trogdord.host'),
			config('trogdord.port')
		);
	}

	/*************************************************************************/

	/**
	 * Return generalized information and statistics about the game server,
	 * such as the number of games running.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getInfo(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->statistics());
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently existing games.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGames(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->games());
	}

	/*************************************************************************/

	/**
	 * Create a new game.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createGame(): \Illuminate\Http\JsonResponse {

		$validator = Validator::make($this->request->all(), [
			'name' => 'bail|required',
			'definition' => 'bail|required'
		], self::CREATE_GAME_INPUT_ERRORS);

		if ($validator->fails()) {
			return response()->json([
				'error' => $validator->errors()->first()
			], 400);
		}

		try {

			$game = $this->trogdord->newGame(
				$this->request->post('name'),
				$this->request->post('definition')
			);

			// Admin has specified that the game should begin running
			// immediately after instantiation
			if (false !== $this->request->post('autostart', false)) {
				$game->start();
			}

			return response()->json([
				'id' => $game->id
			]);
		}

		catch (\Trogdord\NetworkException $e) {
			return response()->json([
				'error' => $e->getMessage()
			], 500);
		}

		catch (\Trogdord\RequestException $e) {
			return response()->json([
				'error' => $e->getMessage(),
				'code'  => $e->getCode()
			], 500);
		}
	}

	/*************************************************************************/

	/**
	 * Return the details of a persistent game.
	 *
	 * @param int $id Game ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function getGame(int $id): \Illuminate\Http\JsonResponse {

		if ($game = \Trogdor\Game::get($id)) {

			return response()->json([
				'id'     => $id,
				'name'   => $game->getMeta('name'),
				'title'  => $game->getMeta('title'),
				'author' => $game->getMeta('author'),
				'time'   => $game->getTime()
			]);
		}

		else {
			return response()->json([
				'id'    => $id,
				'error' => self::GAME_404_MESSAGE
			], 404);
		}
	}

	/*************************************************************************/

	/**
	 * Destroy a persistent game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroyGame(int $id): \Illuminate\Http\JsonResponse {

		// Depersisting a game causes the underlying C++ game object to be
		// destroyed when $game goes out of scope
		if ($game = \Trogdor\Game::get($id)) {
			$game->depersist();
			return response()->json([], 204);
		}

		else {
			return response()->json([
				'id'    => $id,
				'error' => self::GAME_404_MESSAGE
			], 404);
		}
	}

	/*************************************************************************/

	/**
	 * Start or restart a game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function startGame(int $id): \Illuminate\Http\JsonResponse {

		if ($game = \Trogdor\Game::get($id)) {
			$game->start();
			return response()->json([], 204);
		}

		else {
			return response()->json([
				'id'    => $id,
				'error' => self::GAME_404_MESSAGE
			], 404);
		}
	}

	/*************************************************************************/

	/**
	 * Stop a game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function stopGame(int $id): \Illuminate\Http\JsonResponse {

		if ($game = \Trogdor\Game::get($id)) {
			$game->stop();
			return response()->json([], 204);
		}

		else {
			return response()->json([
				'id'    => $id,
				'error' => self::GAME_404_MESSAGE
			], 404);
		}
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently uploaded game definitions (XML files that
	 * define the properties of and the entities in the game.)
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getDefinitions(): \Illuminate\Http\JsonResponse {

		$definitions = [];

		foreach (\App\Models\Definition::all() as &$definition) {
			$definitions[] = [
				'id'           => $definition->id,
				'title'        => $definition->title,
				'author'       => $definition->author,
				'createdAt'    => $definition->created_at,
				'updatedAt'    => $definition->updated_at,
				'lastUploaded' => $definition->last_uploaded
			];
		}

		return response()->json($definitions);
	}

	/*************************************************************************/

	/**
	 * Return details associated with an uploaded game definition.
	 *
	 * @param int $id Game Definition ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getDefinition(int $id): \Illuminate\Http\JsonResponse {

		try {

			$definition = \App\Models\Definition::find($id);

			if ($definition) {
				return response()->json([
					'id'           => $id,
					'title'        => $definition->title,
					'author'       => $definition->author,
					'createdAt'    => $definition->created_at,
					'updatedAt'    => $definition->updated_at,
					'lastUploaded' => $definition->last_uploaded
				]);
			}

			else {
				return response()->json([
					'id'    => $id,
					'error' => self::DEFINITION_404_MESSAGE
				], 404);
			}
		}

		catch (\Illuminate\Database\QueryException $e) {
			return $this->throwQueryExceptionAsJson($e);
		}
	}

	/*************************************************************************/

	/**
	 * Deletes a game definition.
	 *
	 * @param int $id Game Definition ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteDefinition(int $id): \Illuminate\Http\JsonResponse {

		try {

			// I checked to see if this would be more optimal with a call to
			// Model::destroy rather than calling Model::find first and then
			// calling Model::delete if it's found in the database, but after
			// digging through the source (6.13 at the time of this writing),
			// that function is retrieving the model before deleting anyway,
			// so it doesn't make a whit of difference either way.
			$definition = \App\Models\Definition::find($id);

			if ($definition) {
				$definition->delete();
				Storage::delete(self::DEFINITIONS_PATH . "/$id.xml");
				return response()->json([], 204);
			}

			else {
				return response()->json([
					'id'    => $id,
					'error' => self::DEFINITION_404_MESSAGE
				], 404);
			}
		}

		catch (\Illuminate\Database\QueryException $e) {
			return $this->throwQueryExceptionAsJson($e);
		}
	}

	/*************************************************************************/

	/**
	 * Modify a game definition's associated meta data (to upload a new file,
	 * make a request that results in a call to uploadDefinition.)
	 *
	 * @param int $id Game Definition ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateDefinition(int $id): \Illuminate\Http\JsonResponse {

		try {

			$definition = \App\Models\Definition::find($id);

			if ($definition) {

				$definition->title = $this->request->input('title', $definition->title);
				$definition->author = $this->request->input('author', $definition->author);

				if ($definition->isDirty()) {
					$definition->save();
				}

				return response()->json([
					'id'           => $definition->id,
					'title'        => $definition->title,
					'author'       => $definition->author,
					'createdAt'    => $definition->created_at,
					'updatedAt'    => $definition->updated_at,
					'lastUploaded' => $definition->last_uploaded
				]);
			}

			else {
				return response()->json([
					'id'    => $id,
					'error' => self::DEFINITION_404_MESSAGE
				], 404);
			}
		}

		catch (\Illuminate\Database\QueryException $e) {
			return $this->throwQueryExceptionAsJson($e);
		}
	}
}
