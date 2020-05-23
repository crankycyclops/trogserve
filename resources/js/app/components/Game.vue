<template>

	<v-card id="game-console">

		<!-- Error message dialog -->
		<v-dialog persistent
			v-model="showErrorDialog"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="goBack()"
		>

			<v-card>

				<v-card-text>

					<v-row align="center" justify="center" :style="!error ? 'display: none;' : ''">
						<v-col cols="12" md="11">
							<span class="error">{{ error }}</span>
						</v-col>
					</v-row>

				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="goBack()"
					>
						Back to Games
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<!-- Dialog to select player name after game is loaded -->
		<v-dialog persistent
			v-model="showPlayerNameDialog"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="goBack()"
		>

			<v-card>

				<v-card-title>Select a Player Name</v-card-title>

				<v-card-text>

					<v-row align="center" justify="center" :style="!error ? 'display: none;' : ''">
						<v-col cols="12" md="11">
							<span class="error">{{ error }}</span>
						</v-col>
					</v-row>

					<v-form ref="playerNameForm">

						<v-text-field
							v-model.trim="game.playerName"
							:counter="playerNameMaxLen"
							:rules="validation.playerName"
							:disabled="game.loading || game.starting"
							placeholder="e.g. JohnDoe"
							required
							outlined
						></v-text-field>

					</v-form>

				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="goBack()"
					>
						Go Back
					</v-btn>

					<v-btn
						text
						color="error"
						@click="startGame()"
					>
						Let's Play!
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<v-card-text id="output-container">

			<div id="status-bar">
				<span class="statistic location">{{ game.status.location }}</span>
				<span class="statistic health" v-if="game.status.maxHealth">Health
					{{ game.status.health }}/{{ game.status.maxHealth }}
				</span>
			</div>

			<div id="output">

				<!-- Display this while we're waiting to load and connect 
				to the game-->
				<v-container fill-height fluid v-if="!game.connected">
  					<v-row align="center" justify="center">
						<v-col>
							<p id="connecting" class="text-center">Connecting...</p>
						</v-col>
					</v-row>
				</v-container>

				<v-container id="output-content">
					<v-row v-for="(message, i) in game.monitor" :key="i">
						<v-col cols="12">
							<span :class="'command' == message.channel ? 'bold' : ''">
								{{ message.content }}
							</span>
						</v-col>
					</v-row>
				</v-container>

			</div>

			<v-row>

				<!-- This is where the player enters commands -->
				<v-col cols="12">
					<v-text-field
						v-model="game.command"
						:label="game.connected ? 'What do you do now?' : ''"
						append-outer-icon="send"
						clear-icon="clear"
						clearable
						:disabled="!game.connected"
						@keyup.enter="send"
						@click:append-outer="send"
						@click:clear="clear"
					/>
				</v-col>

			</v-row>

		</v-card-text>

	</v-card>

</template>

<style lang="scss" scoped>

	#game-console {
		background-color: #303030;
		border: 1px solid #202020;
	}

	#status-bar {
		display: table;
		table-layout: fixed;
		position: relative;
		width: 100%;
		height: 1.8rem;
		padding: 0 10px 0 10px;
		border-top: 1px solid #202020;
		border-left: 1px solid #202020;
		border-right: 1px solid #202020;
		background-color: #d3d3e5;
		color: #000000;
		z-index: 3;
	}

	#status-bar .statistic {
		display: table-cell;
		width: 50%;
	}

	// Positions the top of the text below the status bar
	#output-content {
		padding-top: 0.5rem;
	}

	#output {

		border-left: 1px solid #202020;
		border-right: 1px solid #202020;
		border-bottom: 1px solid #202020;
		background-color: transparent;
		text-shadow: 0 0 2px #989898;

		scrollbar-color: #d3d3e5 #505050;
		scrollbar-width: auto;

		overflow-x: hidden;
		overflow-y: auto;

		hyphens: auto;
		-webkit-hyphens: auto;
		-moz-hyphens: auto;
		-ms-hyphens: auto;

		position: relative;
		z-index: 1;
	}

	#output::-webkit-scrollbar {
		width: 11px;
	}

	#output::-webkit-scrollbar-track {
		background: #505050;
	}

	#output::-webkit-scrollbar-thumb {
		background: #d3d3e5;
	}

	#output .col {
		padding: 7px;
	}

	// Gives the "console" a scanline effect
	#output-container::before {
		display: block;
		position: absolute;
		width: calc(100% - 32px);
		top: 16px;
		left: 16px;
		content: "";
		z-index: 4;
		pointer-events: none;
		background: repeating-linear-gradient(
			0deg,
			rgba(0, 0, 0, 0.15),
			rgba(0, 0, 0, 0.15) 1px,
			transparent 1px,
			transparent 2px
		);
	}

	// Gives the "console" a monitor glare effect
	#output-container::after {
		display: block;
		position: absolute;
		width: calc(100% - 32px);
		top: 16px;
		left: 16px;
		content: "";
		z-index: 0;
		pointer-events: none;
		background: radial-gradient(circle closest-corner at center, #1c1c2e 15%, #000000 100%);
	}

	#connecting {
		font-weight: 700;
		font-size: 1.5em;
	}

	@media only screen and (max-height: 550px) {

		#output {
			height: 47vh;
		}

		#output-container::before, #output-container::after {
			height: calc(47vh + 1.8rem);
		}
	}

	@media only screen and (min-height: 551px) and (max-height: 699px) {

		#output {
			height: 55vh;
		}

		#output-container::before, #output-container::after {
			height: calc(55vh + 1.8rem);
		}
	}

	@media only screen and (min-height: 700px) {

		#output {
			height: 65vh;
		}

		#output-container::before, #output-container::after {
			height: calc(65vh + 1.8rem);
		}
	}

	// Hide all status bar data other than location if the screen is too narrow)
	@media only screen and (max-width: 359px) {

		#status-bar .health {
			display: none;
		}

		#status-bar .statistic {
			width: 100%;
		}
	}

</style>

<script>

	export default {

		mounted() {

			this.loadGame();
		},

		computed: {

			// Form validation values
			playerNameMaxLen() {return window.playerNameMaxLen;},
			playerNameMaxLenMsg() {return window.playerNameMaxLenMsg;}
		},

		data() {

			return {

				// Toggles the error message dialog
				showErrorDialog: false,

				// Toggles player name selection
				showPlayerNameDialog: false,

				// Keeps track of player names that are known to be unavailable
				unavailableNames: new Set(),

				// If an error occurs while attempting to join the game, set
				// it here.
				error: '',

				// Validation rules for the form fields above
				validation: {

					playerName: [
						v => !!v || "Select a name to continue",
						v => (v || '').trim().length > 0 || "Select a name to continue",
						v => (v || '').length <= this.playerNameMaxLen || this.playerNameMaxLenMsg,
						v => !this.unavailableNames.has((v || '').trim()) || "Player name is not available"
					],
				},

				// Game data
				game: {

					// Information that belongs in the status bar
					status: {

						// The player's current location as displayed in the game
						location: '',

						// The player's health information
						health: null,
						maxHealth: null
					},

					// The text currently displayed in the command input field
					command: '',

					// Will contain the player's name once it's been
					// selected
					playerName: '',

					// Once the game is loaded and a player name has been
					// selected, this will store a websocket connection to
					// sockserve
					socket: null,

					// Whether or not we're attempting to register the
					// player's name and start the game
					starting: false,

					// Whether or not we're loading the game's data
					loading: true,

					// Set this to true once the game has been started
					started: false,

					// Set this to true once we've successfully chosen a
					// player name and connected to sockserve
					connected: false,

					// Represents messages to be written to the "monitor"
					monitor: []
				}
			};
		},

		methods: {

			// Call this if there's an error with the websocket
			handleSocketError() {

				this.error = 'A connection error occured. Refresh the page and try again.';

				if (!this.showPlayerNameDialog) {
					this.showErrorDialog = true;
				}
			},

			// Loads the game
			loadGame() {

				// Reset player name for the about-to-be-loaded game
				this.game.playerName = '';

				axios.get('/api/games/' + this.$router.currentRoute.params.id)

					.then(response => {

						this.$store.commit('setTitle', response.data.title);
						this.game.loading = false;

						// TODO: we'll need to create a player and then
						// websockets before we can enable commands and the
						// display
						this.showSelectPlayerName();
					})

					.catch(error => {

						if (error.response && 404 == error.response.status) {
							this.$store.commit('setError', 'Game not found');
						} else {
							this.$store.commit('setError', 'An internal error occurred. Please wait a few minutes and try again.');
						}

						this.$emit('navigate', '/games', true);
					});
			},

			// Prompt the user to enter a player name before continuing
			showSelectPlayerName() {

				this.showPlayerNameDialog = true;
			},

			// Cancel game play and go back to the list of games
			goBack() {

				this.$emit('navigate', '/games');
			},

			// Attempt to register the player's name and start the game
			startGame() {

				this.error = '';

				if (!this.$refs.playerNameForm.validate()) {
					return;
				}

				// TODO: force https and get hostname,port from config file
				this.game.socket = new WebSocket('ws://localhost:9000/');

				this.game.socket.onopen = event => {
					this.game.socket.send(JSON.stringify({
						gameId: parseInt(this.$router.currentRoute.params.id),
						name: this.game.playerName
					}));
				};

				this.game.socket.onclose = () => {

					this.game.connected = false;

					if (this.game.started) {
						this.handleSocketError();
					}
				};

				this.game.socket.onmessage = event => {

					let data = JSON.parse(event.data);

					if (data.error) {

						this.game.socket.close();

						// Let the user know they can't use that name
						if (409 == data.code) {
							this.unavailableNames.add(this.game.playerName);
							this.$refs.playerNameForm.validate();
						}

						else {
							this.error = data.error;
						}
					}

					else if (data.status && data.status == 'ready') {

						this.showPlayerNameDialog = false;
						this.game.started = true;
						this.game.connected = true;

						this.game.socket.onmessage = event => {
							this.output(JSON.parse(event.data));
						};
					}

					else {
						this.game.error = 'An unknown error occurred. Wait a few minutes and try again.';
						this.socket.close();
					}
				};

				this.game.socket.onerror = this.handleSocketError;
			},

			// Clears the command input field
			clear() {

				this.game.command = '';
			},

			// Prints a formatted output message from the game
			output(message) {

				if ('prompt' != message.channel) {

					switch (message.channel) {

						// Update the status bar with the player's location
						case 'location':
							this.game.status.location = message.content;
							break;

						// Update the status bar with the player's health
						case 'health':
							let json = JSON.parse(message.content);
							this.game.status.health = json.health;
							this.game.status.maxHealth = json.maxHealth;
							break;

						// This is an ordinary message that should be
						// appended to the console
						default:
							this.game.monitor.push(message);
							this.autoScroll();
							break;
					}
				}
			},

			// Sends command to the game server
			send() {

				if (this.game.command.length) {

					this.game.socket.send(this.game.command.trim());

					// log command to the terminal so the player can see it
					this.output({
						channel: 'command',
						content: '> ' + this.game.command.trim()
					});

					this.clear();
				}
			},

			// Auto scroll the console if the user isn't currently scrolling
			// up to read previous messages.
			autoScroll() {

				let monitor = this.$el.querySelector("#output");

				if (monitor.scrollTop === (monitor.scrollHeight - monitor.clientHeight)) {
					this.$nextTick(() => {
						monitor.scrollTop = monitor.scrollHeight;
					});
				}
			}
		}
	};

</script>
