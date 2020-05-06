<template>

	<v-card>

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

					<v-row align="center" justify="center" :style="!game.error ? 'display: none;' : ''">
						<v-col cols="12" md="11">
							<span class="error">{{ game.error }}</span>
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

		<v-card-text>

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

			</div>

			<v-row>

				<!-- This is where the player enters commands -->
				<v-col cols="12">
					<v-text-field
						v-model="command"
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

<style scoped>

	#output {

		background-color: #2a2a2a;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;

		overflow-x: hidden;
		overflow-y: auto;

		hyphens: auto;
		-webkit-hyphens: auto;
		-moz-hyphens: auto;
		-ms-hyphens: auto;
	}

	#output::-webkit-scrollbar {
		width: 11px;
	}

	#output::-webkit-scrollbar-track {
		background: #505050;
	}

	#output::-webkit-scrollbar-thumb {
		background: #d0d0d0;
	}

	@media only screen and (max-height: 550px) {

		#output {
			height: 47vh;
		}
	}

	@media only screen and (min-height: 551px) and (max-height: 699px) {

		#output {
			height: 55vh;
		}
	}

	@media only screen and (min-height: 700px) {

		#output {
			height: 65vh;
		}
	}

	#connecting {
		font-weight: 700;
		font-size: 1.5em;
	}

</style>

<script>

	export default {

		mounted() {

			this.loadGame();
		},

		computed: {

			// Form validation values
			playerNameMaxLen: function () {return window.playerNameMaxLen;},
			playerNameMaxLenMsg: function () {return window.playerNameMaxLenMsg;}
		},

		data() {

			return {

				// Toggles player name selection
				showPlayerNameDialog: false,

				// The text currently displayed in the command input field
				command: '',

				// Validation rules for the form fields above
				validation: {

					playerName: [
						v => !!v || "Select a name to continue",
						v => (v || '').trim().length > 0 || "Select a name to continue",
						v => (v || '').length <= this.playerNameMaxLen || this.playerNameMaxLenMsg
					],
				},

				// Game data
				game: {

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

					// Set this to true once we've successfully chosen a
					// player name and connected to sockserve
					connected: false
				}
			};
		},

		methods: {

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

				if (!this.$refs.playerNameForm.validate()) {
					return;
				}

				this.game.socket = new WebSocket('ws://localhost:9000/');

				this.game.socket.onopen = event => {
					this.game.socket.send(JSON.stringify({
						gameId: parseInt(this.$router.currentRoute.params.id),
						name: this.game.playerName
					}));
				};

				this.game.socket.onerror = error => {
					console.log(error);
				};

				this.game.socket.onclose = () => {
					this.game.connected = false;
				};

				this.game.socket.onmessage = event => {

					let data = JSON.parse(event.data);

					if (data.error) {
						this.game.error = data.error;
						this.game.socket.close();
					}

					else if (data.status && data.status == 'ready') {

						this.showPlayerNameDialog = false;
						this.game.connected = true;

						this.game.socket.onmessage = event => {
							console.log(JSON.parse(event.data));
						};
					}

					else {
						this.game.error = 'An unknown error occurred. Wait a few minutes and try again.';
						this.socket.close();
					}
				};
			},

			// Clears the command input field
			clear() {

				this.command = '';
			},

			// Sends command to the game server
			send() {

				if (this.command.length) {

					// TODO: submit command
					this.clear();
				}
			}
		}
	};

</script>
