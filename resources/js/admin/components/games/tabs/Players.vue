<template>

	<v-card flat>

		<!-- Dialog to create a new player in the game -->
		<v-dialog
			v-model="createPlayerForm.showCreateDialog"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="cancelCreatePlayer()"
		>

			<v-card>

				<v-card-title>Create Player</v-card-title>

				<v-card-text>

					<v-row align="center" justify="start">

						<v-col cols="12">
							Insert a new player into the game.
						</v-col>

						<message type="error" :message="createPlayerForm.error" />

						<v-col cols="12">

							<v-form ref="createPlayerForm">

								<v-text-field
									v-model.trim="createPlayerForm.newPlayerName"
									:counter="playerNameMaxLen"
									:rules="createPlayerForm.validation.name"
									:disabled="createPlayerForm.submitting"
									label="Name"
									placeholder="Player's name"
									required
									outlined
								></v-text-field>

							</v-form>

						</v-col>

					</v-row>

				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="cancelCreatePlayer()"
					>
						Cancel
					</v-btn>

					<v-btn
						text
						color="error"
						@click="createPlayer()"
					>
						Create Player
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<!-- Dialog to confirm removal of a player -->
		<v-dialog
			v-model="removePlayerForm.showRemoveDialog"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="cancelRemovePlayer()"
		>

			<v-card>

				<v-card-title>Remove Player</v-card-title>

				<v-card-text v-if="playerIsSelected">

					<v-row align="center" justify="start">
						<v-col cols="12">
							You're about to remove {{ players.data[players.selected].name }} from the game. <strong>This action is permanent and cannot be undone.</strong>
						</v-col>
					</v-row>

					<v-row align="center" justify="start">
						<v-col cols="12">
							<v-textarea
								v-model.trim="removePlayerForm.removalMessage"
								label="Removal Message"
								placeholder="This message will be sent to the player when they're removed from the game (optional)"
								outlined
							/>
						</v-col>
					</v-row>

				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="cancelRemovePlayer()"
					>
						Cancel
					</v-btn>

					<v-btn
						text
						color="error"
						@click="removePlayer()"
					>
						Remove Player
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<v-card-text v-if="players.loading">
			<progress-bar :show="players.loading" />
		</v-card-text>

		<!-- Players have loaded (or there was an error) -->
		<template v-else>

			<v-card-title>Players</v-card-title>

			<v-card-subtitle>
				Manage players in the game.
			</v-card-subtitle>

			<v-card-text>

				<message type="error" :message="players.error" />

				<!-- Display the loaded list of players -->
				<v-row align="center" justify="start" v-if="!players.error.length">

					<v-col cols="12">

						<v-row align="center" justify="start">

							<v-col cols="12" sm="6" md="8">

								<!-- Manage a specific player -->
								<template v-if="playerIsSelected">

									<v-card flat>

										<v-card-title>
											{{ players.data[players.selected].name }}
										</v-card-title>

										<v-card-text>
											<!-- TODO: statistical data about player -->
										</v-card-text>

										<v-card-actions>

											<v-btn text color="error" @click="promptRemovePlayer()">
												Remove from Game
											</v-btn>

										</v-card-actions>

									</v-card>

								</template>

								<!-- Explain how to manage players -->
								<template v-else>
									{{ players.data.length ? 'Click on a player.' : 'There are currently no players in the game.' }}
								</template>

							</v-col>

							<v-col cols="12" sm="6" md="4" v-if="players.data.length">

								<!-- List of players in the game -->
								<v-list id="players" max-height="48vh" dense>

									<v-subheader>Current Players</v-subheader>

									<v-list-item-group v-model="players.selected" color="primary">

										<v-list-item
											v-for="(player, i) in players.data"
											:key="i"
										>

											<v-list-item-icon>
												<v-icon>perm_identity</v-icon>
											</v-list-item-icon>

											<v-list-item-content>
												<v-list-item-title v-text="player.name"></v-list-item-title>
											</v-list-item-content>

										</v-list-item>

									</v-list-item-group>

								</v-list>

							</v-col>

						</v-row>

					</v-col>

				</v-row>

			</v-card-text>

			<v-card-actions>

				<v-btn text color="primary" @click="loadPlayers()">
					Refresh
				</v-btn>

				<v-btn text color="primary" @click="promptCreatePlayer()">
					Create Player
				</v-btn>

			</v-card-actions>

		</template>

	</v-card>

</template>

<style scoped>

	#players {
		background-color: #2a2a2a;
		border: 1px solid #0e0e0e;
		overflow-y: auto;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;
	}

	#players::-webkit-scrollbar {
		width: 11px;
	}

	#players::-webkit-scrollbar-track {
		background: #505050;
	}

	#players::-webkit-scrollbar-thumb {
		background: #d0d0d0;
	}

</style>

<script>

	import Progress from '../../ui/Progress';
	import Message from '../../ui/Message';
	import RequestMixin from '../../../mixins/Request.vue';

	export default {

		mounted() {

			this.loadPlayers();
		},

		computed: {

			// Form validation values and error messages
			playerNameMaxLen() {return window.playerNameMaxLen;},
			playerNameMaxLenMsg() {return window.playerNameMaxLenMsg;},

			// Returns true if a player was selected (clicked on) in the list
			playerIsSelected() {
				return this.players.selected || 0 === this.players.selected;
			}
		},

		data() {

			return {

				removePlayerForm: {

					// Toggles the "Remove Player" dialog
					showRemoveDialog: false,

					// If set, this is the message that will be sent to a user
					// before they're removed from the game
					removalMessage: ''
				},

				createPlayerForm: {

					// Toggles the "Create Player" dialog
					showCreateDialog: false,

					// When creating a new player, this corresponds to
					// the text field where the admin sets the
					// player's name
					newPlayerName: '',

					// Set to true when we're submitting the "Create
					// Player" form
					submitting: false,

					// If an error occurred when creating a player, set it
					// here.
					error: '',

					// Validation for the "Create Player" form
					validation: {

						name: [
							v => !!v || "You must give the player a name",
							v => (v || '').length <= this.playerNameMaxLen || this.playerNameMaxLenMsg
						]
					}
				},

				players: {

					// Points to the currently selected player
					selected: null,

					// Indicates whether or not we're in the middle of
					// loading a list of players
					loading: true,

					// If an error occurs during loading, set it here
					error: '',

					// Our loaded list of players
					data: []
				},
			};
		},

		methods: {

			// Prompt the admin to create a new player
			promptCreatePlayer() {

				this.createPlayerForm.newPlayerName = '';
				this.createPlayerForm.showCreateDialog = true;
				this.$nextTick(() => {
					this.$refs.createPlayerForm.reset();
				});
			},

			// Cancel the creation of a player
			cancelCreatePlayer() {

				this.createPlayerForm.showCreateDialog = false;
			},

			// API call to create a new player
			createPlayer() {

				if (!this.$refs.createPlayerForm.validate()) {
					return false;
				}

				let gameId = this.$router.currentRoute.params.id;

				this.createPlayerForm.submitting = true;
				this.createPlayerForm.error = '';

				axios
					.post('/admin/api/games/' + gameId + '/players', {name: this.createPlayerForm.newPlayerName})

					.then(response => {

						this.cancelCreatePlayer();
						this.loadPlayers();
					})

					// We can't remove the player, so let the admin know
					// about the error and close the dialog
					.catch(error => {
						this.createPlayerForm.error = this.getResponseError(error);
					})

					.finally(() => {
						this.createPlayerForm.submitting = false;
					});
			},

			// Prompt the admin to remove a player from the game
			promptRemovePlayer() {

				this.removePlayerForm.removalMessage = '';
				this.removePlayerForm.showRemoveDialog = true;
			},

			// Cancel the removal of a player
			cancelRemovePlayer() {

				this.removePlayerForm.showRemoveDialog = false;
			},

			// API call to remove player from the game
			removePlayer() {

				let data = {};

				let gameId = this.$router.currentRoute.params.id;
				let playerName = encodeURIComponent(this.players.data[this.players.selected].name);

				if (this.removePlayerForm.removalMessage) {
					data.message = this.removePlayerForm.removalMessage;
				}

				this.players.loading = true;
				this.players.error = '';

				axios
					.delete('/admin/api/games/' + gameId + '/players/' + playerName, {data: data})

					.then(response => {
						this.loadPlayers();
					})

					// We can't remove the player, so let the admin know
					// about the error and close the dialog
					.catch(error => {
						alert(this.getResponseError(error));
					})

					.finally(() => {
						this.cancelRemovePlayer();
					});
			},

			// Loads list of players
			loadPlayers() {

				this.players.loading = true;
				this.players.error = '';
				this.players.selected = '';

				axios
					.get('/admin/api/games/' + this.$router.currentRoute.params.id + '/players')

					.then(response => {
						this.players.data = response.data;
					})

					// We can't load the players, so display the error
					.catch(error => {
						this.players.error = this.getResponseError(error);
					})

					.finally(() => {
						this.players.loading = false;
						this.players.selected = null;
					});
			}
		},

		components: {
			'message': Message,
			'progress-bar': Progress
		},

		mixins: [
			RequestMixin
		]
	};

</script>
