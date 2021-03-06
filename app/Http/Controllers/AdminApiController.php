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
	 * Return a list of non-sensitive configuration options set in trogdord.ini.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getConfig(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->config());
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
	 * Dumps the server's state to disk.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function dump(): \Illuminate\Http\JsonResponse {

		$this->trogdord->dump();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Restores the server's state from disk.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function restore(): \Illuminate\Http\JsonResponse {

		$this->trogdord->restore();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Return a list of all currently existing games.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGames(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->games(null, ["title", "author", "synopsis"]));
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
			'id'         => $game->id,
			'name'       => $game->name,
			'definition' => $game->definition,
			'created'    => $game->created,
			'title'      => $meta['title'],
			'author'     => $meta['author'],
			'synopsis'   => $meta['synopsis'],
			'statistics' => $game->statistics()
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
	 * Gets one or more meta values (blank values indicate the meta value
	 * doesn't exist.)
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getMeta(int $id): \Illuminate\Http\JsonResponse {

		$game = $this->trogdord->getGame($id);
		$keys = [];

		foreach ($this->request->post() as $key) {
			$keys[] = $key;
		}

		return response()->json($game->getMeta($keys));
	}

	/*************************************************************************/

	/**
	 * Sets (or updates) one or more meta values.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function setMeta(int $id): \Illuminate\Http\JsonResponse {

		$game = $this->trogdord->getGame($id);

		// *** Danger, Will Robinson! This can be dangerous as this data is
		// unsanitized. This is okay for now, as I'm only using it in the
		// admin panel, but if I ever call this through a public facing front
		// end, I'll need to figure out exactly how this data should be
		// sanitized and do that before setting it.
		$game->setMeta($this->request->post());
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Gets a list of all players in the specified game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getPlayers(int $id): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->getGame($id)->players(), 200);
	}

	/*************************************************************************/

	/**
	 * Creates a player in the specified game with the given name.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createPlayer(int $id): \Illuminate\Http\JsonResponse {

		$validator = Validator::make($this->request->all(), [
			'name'     => 'bail|required|max:' . config('validation.newGame.playerNameMaxLen'),
		], [
			'required' => 'Missing required player name',
			'name.max' => config('validation.newGame.playerNameMaxLenMsg')
		]);

		if ($validator->fails()) {
			return response()->json([
				'error' => $validator->errors()->first()
			], 400);
		}

		$this->trogdord->getGame($id)->createPlayer($this->request->post('name'));
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Remove a player from the specified game.
	 *
	 * @param int $id Game ID
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function removePlayer(int $id, string $name): \Illuminate\Http\JsonResponse {

		// The message the player will see before being removed from the game
		$removalMessage = $this->request->post('message') ? $this->request->post('message') : '';

		$this->trogdord->getGame($id)->getPlayer($name)->destroy($removalMessage);
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

	/*************************************************************************/

	/**
	 * Dumps a game to disk.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function dumpGame(int $id): \Illuminate\Http\JsonResponse {

		$this->trogdord->getGame($id)->dump();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Retrieves a list of all game dumps.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getDumps(): \Illuminate\Http\JsonResponse {

		return response()->json($this->trogdord->dumped());
	}

	/*************************************************************************/

	/**
	 * Retrieves a game dump's details.
	 *
	 * @param int $id Game Dump ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function getDump(int $id): \Illuminate\Http\JsonResponse {

		$dump = $this->trogdord->getDump($id);

		return response()->json([
			'id'         => $dump->id,
			'name'       => $dump->name,
			'definition' => $dump->definition,
			'created'    => $dump->created
		]);
	}

	/*************************************************************************/

	/**
	 * Deletes a game dump.
	 *
	 * @param int $id Game Dump ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function destroyDump(int $id): \Illuminate\Http\JsonResponse {

		$this->trogdord->getDump($id)->destroy();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Restores a game dump from disk.
	 *
	 * @param int $id Game Dump ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function restoreDump(int $id): \Illuminate\Http\JsonResponse {

		$dump = $this->trogdord->getDump($id);
		$game = $dump->restore();

		return response()->json([
			'id'         => $game->id,
			'name'       => $game->name,
			'definition' => $game->definition,
			'created'    => $game->created
		]);
	}

	/*************************************************************************/

	/**
	 * Gets a list of a game dump's slots.
	 *
	 * @param int $id Game Dump ID
	 * @return Illuminate\Http\JsonResponse
	 */
	public function getSlots(int $id): \Illuminate\Http\JsonResponse {

		$dump = $this->trogdord->getDump($id);
		return response()->json($dump->slots());
	}

	/*************************************************************************/

	/**
	 * Gets details of a specific dump slot.
	 *
	 * @param int $id Game Dump ID
	 * @param int $id Dump Slot
	 * @return Illuminate\Http\JsonResponse
	 */
	public function getSlot(int $id, int $slot): \Illuminate\Http\JsonResponse {

		$slot = $this->trogdord->getDump($id)->getSlot($slot);

		return response()->json([
			'slot'         => $slot->slot,
			'timestamp_ms' => $slot->timestampMs
		]);
	}

	/*************************************************************************/

	/**
	 * Deletes a specific dump slot (results in the deletion of the whole dump
	 * history if it's the only slot left.)
	 *
	 * @param int $id Game Dump ID
	 * @param int $id Dump Slot
	 * @return Illuminate\Http\JsonResponse
	 */
	public function deleteSlot(int $id, int $slot): \Illuminate\Http\JsonResponse {

		$this->trogdord->getDump($id)->getSlot($slot)->destroy();
		return response()->json([], 204);
	}

	/*************************************************************************/

	/**
	 * Restores a specific dump slot.
	 *
	 * @param int $id Game Dump ID
	 * @param int $id Dump Slot
	 * @return Illuminate\Http\JsonResponse
	 */
	public function restoreSlot(int $id, int $slot): \Illuminate\Http\JsonResponse {

		$this->trogdord->getDump($id)->getSlot($slot)->restore();
		return response()->json([], 204);
	}
}
