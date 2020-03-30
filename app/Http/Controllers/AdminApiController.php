<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class AdminApiController extends Controller {

	// Validation rule => error message mapping for game creation input parameters
	protected const CREATE_GAME_INPUT_ERRORS = [
		'regex'     => 'Please input a valid game definition ID',
		'required'  => 'Missing one or more required parameters',
	];

	// Instance of \Illuminate\Http\Request
	protected $request;

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

		return response()->json($this->trogdord->games(["title", "author"]));
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

		$meta = [];

		if ($this->request->post('title')) {
			$meta['title'] = $this->request->post('title');
		}

		if ($this->request->post('author')) {
			$meta['author'] = $this->request->post('author');
		}

		$game = $this->trogdord->newGame(
			$this->request->post('name'),
			$this->request->post('definition'),
			$meta
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

	/*************************************************************************/

	/**
	 * Return the details of an existing game.
	 *
	 * @param int $id Game ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function getGame(int $id): \Illuminate\Http\JsonResponse {

		$game = $this->trogdord->getGame($id);
		$meta = $game->getMeta(["title", "author"]);

		return response()->json([
			'id'     => $game->id,
			'name'   => $game->name,
			'title'  => $meta['title'],
			'author' => $meta['author'],
			// TODO: add current game time and whether or not game is started
		]);
	}

	/*************************************************************************/

	/**
	 * Destroy a game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroyGame(int $id): \Illuminate\Http\JsonResponse {

		$this->trogdord->getGame($id)->destroy();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Start or restart a game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function startGame(int $id): \Illuminate\Http\JsonResponse {

		$this->trogdord->getGame($id)->start();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Stop a game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function stopGame(int $id): \Illuminate\Http\JsonResponse {

		$this->trogdord->getGame($id)->stop();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently available game definitions (XML files
	 * that define the properties of and the entities in the game.)
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getDefinitions(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->definitions());
	}
}
