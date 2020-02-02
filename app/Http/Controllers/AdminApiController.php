<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminApiController extends Controller {

	// What error message to display when a non-existent game ID is passed into
	// a controller action.
	protected const GAME_404_MESSAGE = 'Game not found';

	// What error message to display when a non-existent definition ID is passed
	// into a controller action.
	protected const DEFINITION_404_MESSAGE = 'Game definition not found';

	// If we're in production mode and a query error occurs, this is the
	// generic message we should return to the user.
	protected const GENERIC_500_MESSAGE = 'An error occured. Please try your query again in a few minutes.';

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
	}

	/*************************************************************************/

	/**
	 * Return generalized information and statistics about the game server,
	 * such as the number of games running.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getInfo(): \Illuminate\Http\JsonResponse {

		return response()->json(\Trogdor\Game::info());
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently persistent games.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGames(): \Illuminate\Http\JsonResponse {

		$data = [];

		foreach (\Trogdor\Game::getAll() as &$game) {
			$data[] = [
				'id' => $game->getPersistentId(),
				'title' => $game->getMeta('title'),
				'author' => $game->getMeta('author')
			];
		}

		return response()->json($data);
	}

	/*************************************************************************/

	/**
	 * Create a new persistent game.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createGame(): \Illuminate\Http\JsonResponse {

		// TODO: stub
		return response()->json([
			'id' => 0
		]);
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
				'id' => $id,
				'title' => $game->getMeta('title'),
				'author' => $game->getMeta('author')
			]);
		}

		else {
			return response()->json([
				'id' => $id,
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
				'id' => $id,
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
				'id' => $id,
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
				'id' => $id,
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
	 * Creates a new game definition entry (must be followed by a request
	 * resulting in a call to uploadDefinition.)
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createDefinition(): \Illuminate\Http\JsonResponse {

		try {

			$definition = new \App\Models\Definition([
				'title'  => $this->request->input('title', ''),
				'author' => $this->request->input('author', '')
			]);

			$definition->save();

			return response()->json([
				'id' => $definition->id
			]);
		}

		catch (\Illuminate\Database\QueryException $e) {
			return $this->throwQueryExceptionAsJson($e);
		}
	}

	/*************************************************************************/

	/**
	 * Uploads a game definition once an entry for it has been created.
	 *
	 * @param int $id Game Definition ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function uploadDefinition(int $id): \Illuminate\Http\JsonResponse {

		// TODO: stub
		return response()->json([], 204);
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
					'id' => $id,
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
				return response()->json([], 204);
			}

			else {
				return response()->json([
					'id' => $id,
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

		// TODO: stub
		try {
			return response()->json([
				'id' => 0,
				'title' => 'Super Funtime Game',
				'author' => 'James Colannino'
			]);
		}

		// TODO: catch the appropriate exception type
		catch (Exception $e) {
			return response()->json([
				'id' => $id,
				'error' => self::DEFINITION_404_MESSAGE
			], 404);
		}
	}
}
