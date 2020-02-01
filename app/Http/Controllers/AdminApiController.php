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

	/*************************************************************************/

	/**
	 * Return generalized statistics about the game server, such as the number
	 * of games running.
	 *
	 * @return array
	 */
	public function getStatistics() {

		// TODO: modify my extension to add static methods for retrieving these
		// numbers.
		return [
			'totalGameCount' => 0,
			'totalGamesRunning' => 0,
			'totalGamesStopped' => 0
		];
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently persistent games.
	 *
	 * @return array
	 */
	public function getGames() {

		// TODO: stub
		return [
			[
				'id' => 0,
				'title' => 'Super Funtime Game'
			]
		];
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
				'title' => $game->getMeta('title')
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

		// TODO: stub
		return [
			[
				'id' => 0,
				'title' => 'Super Funtime Game',
				'lastUploadedOn' => time()
			]
		];
	}

	/*************************************************************************/

	/**
	 * Uploads a game definition along with associated meta data.
	 *
	 * @return array
	 */
	public function uploadDefinition() {

		// TODO: stub
		return [
			'id' => 0
		];
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
				'lastUploadedOn' => time()
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
	 * Modify a game definition.
	 *
	 * @return array | \Illuminate\Http\Response
	 */
	public function modifyDefinition(int $id) {

		// TODO: stub
		try {
			return [
				'id' => 0,
				'title' => 'Super Funtime Game',
				'lastUploadedOn' => time()
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
