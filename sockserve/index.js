const HANDSHAKE_SUCCESSFUL = '{"status":"ready"}';
const VALID_PLAYER_NAME = /^[A-Za-z0-9 -_]+$/;

const EXIT_SUCCESS = 0;
const EXIT_FAILURE = 1;

// TODO: make these configurable
const REDIS_INPUT_CHANNEL = 'trogdord:in';
const REDIS_OUTPUT_CHANNEL = 'trogdord:out';

const Trogdord = require('trogdord');
const Redis = require('redis');
const WebSocketServer = require('ws').Server;

/*****************************************************************************/

class SockServe {

	// Instance of Trogdord
	#trogdord;

	// Instance of WebSocketServer
	#wss;

	// Sends player input to trogdord
	#publisher;

	// Listens for messages from trogdord
	#subscriber;

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

		// If the client doesn't know how to talk to the server or sends
		// invalid data during the handshake, don't bother trying to
		// communicate further.
		if (!data.gameId || !data.name ||
		'number' != typeof data.gameId || !data.gameId.isInteger() ||
		!data.name.match(VALID_PLAYER_NAME)) {
			socket.close();
		}

		else if (sockets[data.gameId][data.name]) {
			socket.send(JSON.stringify({error: 'Player name is unavailable'}));
			socket.close();
		}

		// The redis subscriber's event listener will see this buffer exists
		// and stash the first few messages here until the handshake is
		// complete and the server is ready to send the messages to the client.
		this.#messageBuffer[data.gameId][data.name] = [];

		this.#trogdord.getGame(data.gameId)

			.then(game => {
				socket.game = game;
				return game.createPlayer(data.name);
			})

			.then(player => {

				socket.player = player;
				sockets[data.gameId][data.name] = socket;

				// Socket will listen for input from the player
				sockets.on('message', this.#processCommand);

				// TODO: this might not work, because the redis listener
				// might not insert until after. Have to test this. If it
				// doesn't work, will have to figure out what kind of delay
				// to use. HOWEVER, I think a setTimeout with 0 delay will
				// delay the execution of this until it's ready, so try that!
				this.#messageBuffer[data.gameId][data.name].forEach(message => {
					socket.send(message);
				});

				// See comment above
				delete this.#messageBuffer[data.gameId][data.name];
			})

			.catch(error => {
				send(error.message);
				socket.close();
			});
	}

	/**
	 * Initializes and starts the WebSocket server.
	 */
	constructor() {

		// TODO: make port and host for redis server configurable
		this.#publisher = Redis.createClient(6379, 'localhost');
		this.#subscriber = Redis.createClient(6379, 'localhost');;

		// TODO: make hostname and port configurable, and also add
		// configuration for connection timeout, which will use third
		// parameter
		this.#trogdord = new Trogdord('localhost', 1040);

		// TODO: force https and make configurable for port, certificate, etc
		this.#wss = new WebSocketServer({
			port: 9000
		});

		this.#subscriber.subscribe(REDIS_OUTPUT_CHANNEL);

		console.log('Connecting to trogdord...');

		this.#trogdord.on('connect', () => {
		
			console.log('Connected.');

			this.#wss.on('connection', socket => {

				// When the socket is first established, we need to negotiate the
				// connection.
				socket.on('connection', () => {
					socket.once('message', message => {
						this.#handshake(socket, message);
					});
				});

				// When the socket is closed, remove the player from the game and
				// do some cleanup.
				socket.on('close', () => {
					if (socket.game && socket.player) {
						socket.player.destroy();
						delete sockets[socket.game.id][socket.player.name];
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

		// Send output messages from trogdord to the client
		this.#subscriber.on('message', (channel, message) => {

			if (this.#sockets[message.game_id][message.entity]) {
				this.#sockets[message.game_id][message.entity].send(message);
			}
	
			// A player has just been created, resulting in messages we can't send right away
			else if (this.#messageBuffer[message.game_id][message.entity]) {
				this.#messageBuffer[message.game_id][message.entity].push(message);
			}
		});

		// An error occurred when attempting to connect to redis
		// TODO: is just exiting a good idea? Attempting to re-connect might be better
		this.#publisher.on('error', error => {
			console.log(error);
			process.exit(EXIT_FAILURE);
		});

		this.#subscriber.on('error', error => {
			console.log(error);
			process.exit(EXIT_FAILURE);
		});
	}

	/**
	 * Stop the WebSocket server and close all our connections.
	 */
	shutdown() {

		if (this.#running) {

			this.#running = false;

			this.#trogdord.close();
			this.#subscriber.unsubscribe();
			this.#subscriber.quit();
			this.#publisher.quit();
		}
	}
};

/*****************************************************************************/

const server = new SockServe();

process.on('SIGINT', () => {
	server.shutdown();
	process.exit(EXIT_SUCCESS);
});
