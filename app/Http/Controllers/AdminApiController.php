<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class AdminApiController extends Controller {

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

		return response()->json($this->trogdord->games(["title", "author", "synopsis"]));
	}

	/*************************************************************************/

	/**
	 * Create a new game.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createGame(): \Illuminate\Http\JsonResponse {

		$validator = Validator::make($this->request->all(), [
			'name'       => 'bail|required|max:' . config('validation.newGame.nameMaxLen'),
			'definition' => 'bail|required',
			'title'      => 'bail|max:' . config('validation.newGame.titleMaxLen'),
			'author'     => 'bail|max:' . config('validation.newGame.authorMaxLen'),
			'synopsis'   => 'bail|nullable|max:' . config('validation.newGame.synopsisMaxLen')
		], [
			'required'     => 'Missing one or more required parameters',
			'name.max'     => config('validation.newGame.nameMaxLenMsg'),
			'synopsis.max' => config('validation.newGame.synopsisMaxLenMsg'),
			'title.max'    => config('validation.newGame.titleMaxLenMsg'),
			'author.max'   => config('validation.newGame.authorMaxLenMsg')
		]);

		if ($validator->fails()) {
			return response()->json([
				'error' => $validator->errors()->first()
			], 400);
		}

		$meta = [];

		foreach (['title', 'author', 'synopsis'] as $metaKey) {
			if ($this->request->post($metaKey)) {
				$meta[$metaKey] = $this->request->post($metaKey);
			}
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
		$meta = $game->getMeta(['title', 'author', 'synopsis']);

		return response()->json([
			'id'       => $game->id,
			'name'     => $game->name,
			'title'    => $meta['title'],
			'author'   => $meta['author'],
			'synopsis' => $meta['synopsis']
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
