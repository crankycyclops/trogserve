<template>

	<v-card flat>

		<!-- Dialog to confirm destruction or restoration of a dumped game -->
		<v-dialog
			v-model="dialog.show"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="dialog.show = false;"
		>

			<v-card>

				<v-card-title>{{ 'destroy' == dialog.operation ? 'Destroy' : 'Restore' }} Game</v-card-title>

				<v-card-text>
					Are you <strong>*sure*</strong> you want to {{ 'destroy' == dialog.operation ? 'destroy' : 'restore' }}
					this dumped game? This action is permanent and cannot be undone{{
						'destroy' == dialog.operation ? ', and all entities and players will be lost.' : '. If a game with the same id already exists, it will be overwritten, removing any players or other entities that were added since the game was last dumped.'
					}}
				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="dialog.show = false;"
					>
						Cancel
					</v-btn>

					<v-btn
						text
						color="error"
						@click="proceed()"
					>
						Proceed
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<v-dialog class="error"
			v-model="dialog.showError"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="dialog.showError = false;"
		>

			<v-card>

				<v-card-text class="restore">{{ dialog.errorMessage }}</v-card-text>

				<v-card-actions>

					<v-btn class="restore"
						text
						x-large
						color="primary"
						@click="dialog.showError = false;"
					>
						OK
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<v-card-title>Dumped Games</v-card-title>

		<v-card-subtitle>
			Manage and restore dumped games.
		</v-card-subtitle>

		<v-card-text>

			<!-- Loading progress -->
			<v-row align="center" justify="start" v-if="status.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<!-- Display this after loading the page (or an error occurred) -->
			<template v-else>

				<v-row align="center" justify="start" v-if="status.error">
					<v-col cols="12">
						<span class="error">Unable to connect to trogdord.</span>
					</v-col>
				</v-row>

				<template v-else>

					<!-- Call was successful, but there are no dumped games -->
					<v-row align="center" justify="start" v-if="!games.length">
						<v-col cols="12">
							<span class="warning">There aren't any dumped games yet.</span>
						</v-col>
					</v-row>

					<template v-else>

						<v-row style="font-size: 1.1rem;">
							<v-col xs="12">Click "Restore" to recover a game from its most recent slot or "Destroy" to delete the game's entire dump history. To see or operate on individual slots, click on the desired game's name.</strong></v-col>
						</v-row>

						<!-- Clickable list of dumped games -->
						<v-list id="games" three-line max-height="48vh">

							<template v-for="(game, index) in games">

								<v-list-item link :key="game.name" @click="expand(game.id)">

									<v-list-item-content>

										<v-list-item-title>{{ game.name }} ({{ game.definition }})</v-list-item-title>
										<v-list-item-subtitle><strong>Created:</strong> {{ showCreated(game.created) }}</v-list-item-subtitle>

									</v-list-item-content>

									<v-list-item-action>

										<v-btn
											text
											color="primary"
											:disabled="status.disableButtons"
											@click.stop="confirmRestore(game.id)"
										>
											Restore
										</v-btn>

										<v-btn
											text
											color="error"
											:disabled="status.disableButtons"
											@click.stop="confirmDestroy(game.id)"
										>
											Destroy
										</v-btn>

									</v-list-item-action>

								</v-list-item>

								<v-divider v-if="index < games.length - 1" :key="index" />

							</template>

						</v-list>

					</template>

				</template>

			</template>

		</v-card-text>

	</v-card>

</template>


<style>

	#games {
		margin-top: 2rem;
		background-color: #2a2a2a;
		border: 1px solid #0e0e0e;
		overflow-y: auto;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;
	}

</style>


<script>

	export default {

		mounted() {

			this.load();
		},

		data: function () {

			return {

				dialog: {

					// Show the dialog confirming restoration or destruction of a dumped game
					show: false,

					// Will be set to one of the following strings: 'destroy' or 'restore'
					operation: 'destroy',

					// The id of whichever dumped game has been selected
					selectedId: null,

					// Show or hide the error dialog
					showError: false,

					// Error message to display in the error dialog
					errorMessage: ''
				},

				status: {

					// Gets set to false when the page has finished loading
					loading: true,

					// When set to true, the restore and destroy buttons will be disabled
					disableButtons: false
				},

				// Games retrieved by load()
				games: []
			};
		},

		methods: {

			// Loads the page
			load() {

				this.status.loading = true;

				axios.get('/admin/api/config')

					.then(response => {

						if (!response.data["state.enabled"]) {
							this.$emit('navigate', '/admin/state');
						}

						return axios.get('/admin/api/dumps');
					})

					.then(response => {

						this.games = response.data;
					})

					.catch(error => {

						this.$emit('navigate', '/admin/state');
					})

					.finally(() => {

						this.status.loading = false;
					});
			},

			// Confirm restoration of a dumped game
			confirmRestore(id) {

				this.dialog.selectedId = id;
				this.dialog.operation = 'restore';
				this.dialog.show = true;
			},

			// Confirm destruction of a dumped game
			confirmDestroy(id) {

				this.dialog.selectedId = id;
				this.dialog.operation = 'destroy';
				this.dialog.show = true;
			},

			// If an operation was confirmed, carry it out
			proceed() {

				if ('destroy' == this.dialog.operation) {
					this.destroy(this.dialog.selectedId);
				} else {
					this.restore(this.dialog.selectedId);
				}

				this.dialog.show = false;
			},

			// Restore a dumped game
			restore(id) {

				alert('TODO: restore ' + id + "!");
			},

			// Destroy a dumped game
			destroy(id) {

				this.status.disableButtons = true;

				axios.delete("/admin/api/dumps/" + id)

					.then(response => {

						this.load();
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							this.dialog.errorMessage = error.message;
						} else {
							this.dialog.errorMessage = 'An unknown error occurred. Please try again.';
						}

						this.dialog.showError = true;
					})

					.finally(() => {
						this.status.disableButtons = false;
					});
			},

			// Display the details of a game's dump history
			expand(id) {

				alert('TODO: expand(' + id + ')');
			},

			// Display a string representation of a UNIX timestamp
			showCreated(timestamp) {

				return (new Date(timestamp * 1000)).toString();
			}
		}
	};

</script>
