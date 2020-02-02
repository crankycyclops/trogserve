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

	// If we're in production mode and a query error occurs while creating a
	// new game definition, this is the generic message we return to the user.
	protected const CREATE_DEFINITION_QUERY_ERROR_MSG = 'An error occured. Please try your query again.';

	// Instance of \Illuminate\Http\Request
	protected $request;

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
	 * @return array
	 */
	public function getInfo() {

		return \Trogdor\Game::info();
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently persistent games.
	 *
	 * @return array
	 */
	public function getGames() {

		$data = [];

		foreach (\Trogdor\Game::getAll() as &$game) {
			$data[] = [
				'id' => $game->getPersistentId(),
				'title' => $game->getMeta('title'),
				'author' => $game->getMeta('author')
			];
		}

		return $data;
	}

	/*************************************************************************/

	/**
	 * Create a new persistent game.
	 *
	 * @return array
	 */
	public function createGame() {

		// TODO: stub
		return [
			'id' => 0
		];
	}

	/*************************************************************************/

	/**
	 * Return the details of a persistent game.
	 *
	 * @return array | \Illuminate\Http\Response
	 */
	public function getGame(int $id) {

		if ($game = \Trogdor\Game::get($id)) {

			return [
				'id' => $id,
				'title' => $game->getMeta('title'),
				'author' => $game->getMeta('author')
			];
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
	 * @return \Illuminate\Http\Response
	 */
	public function destroyGame(int $id) {

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
	 * @return \Illuminate\Http\Response
	 */
	public function startGame(int $id) {

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
	 * @return \Illuminate\Http\Response
	 */
	public function stopGame(int $id) {

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
	 * @return array
	 */
	public function getDefinitions() {

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

		return $definitions;
	}

	/*************************************************************************/

	/**
	 * Creates a new game definition entry (must be followed by a request
	 * resulting in a call to uploadDefinition.)
	 *
	 * @return array | \Illuminate\Http\Response
	 */
	public function createDefinition() {

		try {

			$definition = new \App\Models\Definition([
				'title'  => $this->request->input('title', ''),
				'author' => $this->request->input('author', '')
			]);

			$definition->save();

			return [
				'id' => $definition->id
			];
		}

		catch (\Illuminate\Database\QueryException $e) {

			if ('production' == config('app.env')) {

				return response()->json([
					'error' => self::CREATE_DEFINITION_QUERY_ERROR_MSG
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
	}

	/*************************************************************************/

	/**
	 * Uploads a game definition once an entry for it has been created.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function uploadDefinition(int $id) {

		// TODO: stub
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Return details associated with an uploaded game definition.
	 *
	 * @return array | \Illuminate\Http\Response
	 */
	public function getDefinition(int $id) {

		// TODO: stub
		try {

			return [
				'id' => 0,
				'title' => 'Super Funtime Game',
				'author' => 'James Colannino',
				'createdAt' => 'TODO',
				'updatedAt' => 'TODO',
				'lastUploaded' => time()
			];
		}

		// TODO: catch the appropriate exception type
		catch (Exception $e) {
			return response()->json([
				'id' => $id,
				'error' => self::DEFINITION_404_MESSAGE
			], 404);
		}
	}

	/*************************************************************************/

	/**
	 * Deletes a game definition.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function deleteDefinition(int $id) {

		// TODO: stub
		try {
			return response()->json([], 204);
		}

		// TODO: catch the appropriate exception type
		catch (Exception $e) {
			return response()->json([
				'id' => $id,
				'error' => self::DEFINITION_404_MESSAGE
			], 404);
		}
	}

	/*************************************************************************/

	/**
	 * Modify a game definition's associated meta data (to upload a new file,
	 * make a request that results in a call to uploadDefinition.)
	 *
	 * @return array | \Illuminate\Http\Response
	 */
	public function updateDefinition(int $id) {

		// TODO: stub
		try {
			return [
				'id' => 0,
				'title' => 'Super Funtime Game',
				'author' => 'James Colannino'
			];
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
