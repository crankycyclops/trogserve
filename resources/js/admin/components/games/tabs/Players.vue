<template>

	<v-card flat>

		<!-- Loading progress -->
		<template v-if="players.loading">

			<v-card-title>Fetching Players&hellip;</v-card-title>

			<v-card-text>

				<v-row align="center" justify="start">
					<v-col cols="12">
						<v-progress-linear :active="true" :indeterminate="true" />
					</v-col>
				</v-row>

			</v-card-text>

		</template>

		<!-- Players have loaded (or there was an error) -->
		<template v-else>

			<v-card-title>Players</v-card-title>

			<v-card-subtitle>
				Manage players in the game.
			</v-card-subtitle>

			<v-card-text>

				<!-- An error occurred -->
				<v-row align="center" justify="start" v-if="players.error">

					<v-col cols="12">
						<span class="error">{{ players.error }}</span>
					</v-col>

				</v-row>

				<!-- Display the loaded list of players -->
				<v-row align="center" justify="start" v-else>

					<v-col cols="12">

						<v-row align="center" justify="start">

							<v-col cols="12" sm="6" md="8">

								<!-- Manage a specific player -->
								<template v-if="players.selected || 0 === players.selected">

									<v-card flat>

										<v-card-title>
											{{ players.data[players.selected].name }}
										</v-card-title>

										<v-card-text>
											<!-- TODO: statistical data about player -->
										</v-card-text>

										<v-card-actions>

											<v-btn text color="error" @click="promptRemove()">
												Remove from Game
											</v-btn>

										</v-card-actions>

									</v-card>

								</template>

								<!-- Explain how to manage players -->
								<template v-else>
									Click on a player.
								</template>

							</v-col>

							<v-col cols="12" sm="6" md="4">

								<!-- List of players in the game -->
								<v-list id="players" max-height="48vh" dense>

									<v-subheader>Players</v-subheader>

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

	import RequestMixin from '../../../mixins/Request.vue';

	export default {

		mounted: function () {

			this.loadPlayers();
			console.log(this.players.data);
		},

		data: function () {

			return {

				players: {

					// Points to the currently selected player
					selected: null,

					// Indicates whether or not we're in the middle of
					// loading a list of players
					loading: true,

					// If an error occurs during loading, set it here
					error: null,

					// Our loaded list of players
					data: []
				}
			};
		},

		methods: {

			// Prompt the admin to remove a player from the game
			promptRemove: function () {

				alert('TODO');
			},

			// Loads list of players
			loadPlayers: function () {

				let self = this;

				this.players.loading = true;
				this.players.error = null;

				axios
					.get('/admin/api/games/' + this.$router.currentRoute.params.id + '/players')

					.then(response => {
						self.players.data = response.data;
					})

					// We can't load the game, so return to the games list
					// page with an error message at the top.
					.catch(error => {
						self.players.error = self.getResponseError(error);
					})

					.finally(() => {
						self.players.loading = false;
						self.players.selected = null;
					});
			}
		},

		mixins: [
			RequestMixin
		]
	};

</script>
