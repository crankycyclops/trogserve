const HANDSHAKE_SUCCESSFUL = '{"status":"ready"}';
const VALID_PLAYER_NAME = /^[A-Za-z0-9 _\-]{1,25}$/;

const EXIT_SUCCESS = 0;
const EXIT_FAILURE = 1;

const Trogdord = require('trogdord');
const Redis = require('redis');
const WebSocketServer = require('ws').Server;

const Config = require('./config');

/*****************************************************************************/

class SockServe {

	// Instance of Trogdord
	#trogdord;

	// Instance of WebSocketServer
	#wss;

	// Listens for output messages from trogdord
	#outputSubscriber;

	// Set to true if the server is running and false if it's being shutdown
	// (this lets the event handler for trogd.on('close') determine whether or
	// not an error has occurred.)
	#running = true;

	// Maps gameId => playerName => websocket
	#sockets = {};

	// Game.createPlayer will result in messages being sent via redis before
	// the socket is mapped to the player and ready to receive messages. A way
	// to work around this is to cache those messages when a socket is first
	// opened so that we can relay the messages after the socket is setup.
	// Maps gameId => playerName => array of messages
	#messageBuffer = {};

	/**
	 * Sends input to trogdord on behalf of a player.
	 *
	 * @param {Object} socket The WebSocket we received the input from
	 * @param {String} command The command to send to trogdord
	 */
	#processCommand = (socket, command) => {

		socket.player.input(command)
			.catch(error => {
				socket.send(JSON.stringify({error: error.message}));
			});
	}

	/**
	 * Attempts to negotiate a connection to a client by creating a player in
	 * the specified game if available and sets up the WebSocket if successful.
	 *
	 * @param {Object} socket The WebSocket we received the input from
	 * @param {Object} message JSON object containing the game's ID and new player's name
	 */
	#handshake = (socket, data) => {

		data = JSON.parse(data);

		// If the client doesn't know how to talk to the server or sends
		// invalid data during the handshake, don't bother trying to
		// communicate further.
		if (undefined == typeof data.gameId || !data.name ||
		'number' != typeof data.gameId || !Number.isInteger(data.gameId) ||
		!data.name.match(VALID_PLAYER_NAME)) {
			socket.send(JSON.stringify({error: 'Invalid data', code: 400}));
			socket.close();
			return;
		}

		else if (this.#sockets[data.gameId] && this.#sockets[data.gameId][data.name]) {
			socket.send(JSON.stringify({error: 'Player name is unavailable', code: 409}));
			socket.close();
			return;
		}

		// The redis subscriber's event listener will see this buffer exists
		// and stash the first few messages here until the handshake is
		// complete and the server is ready to send the messages to the client.
		if (!this.#messageBuffer[data.gameId]) {
			this.#messageBuffer[data.gameId] = {};
		}

		this.#messageBuffer[data.gameId][data.name] = [];

		this.#trogdord.getGame(data.gameId)

			.then(game => {
				socket.game = game;
				return game.createPlayer(data.name);
			})

			.then(player => {

				socket.player = player;

				if (!this.#sockets[data.gameId]) {
					this.#sockets[data.gameId] = {};
				}

				this.#sockets[data.gameId][data.name] = socket;

				// Socket will listen for input from the player
				socket.on('message', message => {
					this.#processCommand(socket, message.toString());
				});

				socket.send(HANDSHAKE_SUCCESSFUL);

				// Retrieve the first few input messages we got when we
				// created the player and output them
				this.#messageBuffer[data.gameId][data.name].forEach(message => {
					socket.send(message);
				});

				delete this.#messageBuffer[data.gameId][data.name];
			})

			.catch(error => {

				let message = 409 == error.status ? 'Player name is unavailable' : error.message;

				socket.send(JSON.stringify({error: message, code: error.status}));
				socket.close();
			});
	}

	/**
	 * Initializes and starts the WebSocket server.
	 */
	constructor() {

		this.#trogdord = new Trogdord(Config.trogdord.host, Config.trogdord.port);

		// TODO: support https
		this.#wss = new WebSocketServer({
			port: Config.socket.port
		});

		console.log('Connecting to trogdord...');

		this.#trogdord.on('connect', () => {
		
			console.log('Connected.');

			this.#wss.on('connection', socket => {

				// When the socket is first established, we need to negotiate the
				// connection.
				socket.once('message', message => {
					this.#handshake(socket, message);
				});

				// When the socket is closed, remove the player from the game and
				// do some cleanup.
				socket.on('close', () => {
					if (socket.game && socket.player) {
						socket.player.destroy().catch((e) => {});
						delete this.#sockets[socket.game.id][socket.player.name];
					}
				});

				// TODO: Is this the right way to handle errors?
				socket.on('error', error => {
					socket.close();
				});
			});
		});

		// Connection to trogdord was closed (could be an error if the server went down
		// unexpectedly)
		this.#trogdord.on('close', () => {

			// trogdord went down when it wasn't supposed to. What's the right way to
			// handle this, and what cleanup do I need to do?
			console.log('Trogdord just went down.');
			this.shutdown();
			process.exit(EXIT_FAILURE);
		});

		// An error occurred when attempting to connect to trogdord
		this.#trogdord.on('error', error => {

			// TODO: What's a proper way to handle this error, and what kind of cleanup
			// do I need to do?
			console.log(error.message);
			process.exit(EXIT_FAILURE);
		});

		this.#outputSubscriber = Redis.createClient(
			Config.redis.output.port,
			Config.redis.output.host
		);

		(async () => {

			// An error occurred when attempting to connect to redis
			// TODO: is just exiting a good idea? Attempting to re-connect might be better
			this.#outputSubscriber.on('error', error => {
				console.log(error);
				process.exit(EXIT_FAILURE);
			});

			await this.#outputSubscriber.connect();

			// Send output messages from trogdord to the client
			await this.#outputSubscriber.subscribe(Config.redis.output.channel, message => {

				let json = JSON.parse(message);

				// We've received a signal that the player has been removed from
				// the game
				if ('removed' == json.channel && this.#sockets[json.game_id][json.entity]) {
					this.#sockets[json.game_id][json.entity].close(1000, 'removed');
					delete this.#sockets[json.game_id][json.entity];
				}

				else if (this.#sockets[json.game_id] &&
					this.#sockets[json.game_id][json.entity]) {
					this.#sockets[json.game_id][json.entity].send(message);
				}

				// A player has just been created, resulting in messages we can't send right away
				else if (this.#messageBuffer[json.game_id] &&
					this.#messageBuffer[json.game_id][json.entity]) {
					this.#messageBuffer[json.game_id][json.entity].push(message);
				}
			});
		})();
	}

	/**
	 * Stop the WebSocket server and close all our connections.
	 */
	shutdown() {

		if (this.#running) {

			this.#running = false;

			this.#trogdord.close();
			this.#outputSubscriber.unsubscribe();
			this.#outputSubscriber.quit();
		}
	}
};

/*****************************************************************************/

const server = new SockServe();

process.on('SIGINT', () => {
	server.shutdown();
	process.exit(EXIT_SUCCESS);
});
