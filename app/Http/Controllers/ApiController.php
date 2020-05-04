<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class ApiController extends Controller {

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
	 * Return a list of all currently existing games. Unlike the equivalent in
	 * AdminApiController, this method will only return games that are running
	 * and which are therefore accessible to the public.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGames(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->games([
			'is_running' => true
		], [
			'title',
			'author',
			'synopsis'
		]));
	}

	/*************************************************************************/

	/**
	 * Return the details of an existing game. Unlike the equivalent request in
	 * AdminApiController, this will only return details for a running (and
	 * therefore publicly accessible) game and will not include the definition
	 * or game-specific statistics.
	 *
	 * @param int $id Game ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function getGame(int $id): \Illuminate\Http\JsonResponse {

		// TODO: right now, I'm making three separate requests to trogdord.
		// I'd like to eventually get this down to 1. I'll have to investigate
		// my architecture and PHP extension and see if there are good ways to
		// do this. Maybe I could support filters in the request used to
		// retrieve a game so that if it's not running, I'll get a not found
		// error from trogdord. That would at least reduce the number of
		// requests here by 1.
		$game = $this->trogdord->getGame($id);

		if (!$game->isRunning()) {
			return response()->json([
				'error' => 'game not found'
			], 404);
		}

		$meta = $game->getMeta(['title', 'author', 'synopsis']);

		return response()->json([
			'id'         => $game->id,
			'name'       => $game->name,
			'title'      => $meta['title'],
			'author'     => $meta['author'],
			'synopsis'   => $meta['synopsis'],
		]);
	}
};
