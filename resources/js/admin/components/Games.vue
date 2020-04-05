<template>

	<v-card>

		<v-card-title>Games</v-card-title>

		<v-card-subtitle>
			Click on a game to edit its details or change its state.
		</v-card-subtitle>

		<v-card-text>

			<!-- Loading progress -->
			<v-row align="center" justify="start" v-if="games.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<!-- Display this after games have loaded (or an error occurred) -->
			<template v-else>

				<!-- API call failed -->
				<v-row align="center" justify="start" v-if="games.error">
					<v-col cols="12">
						<span class="error">{{ games.error }}</span>
					</v-col>
				</v-row>

				<!-- Call was successful, but there are no games -->
				<v-row align="center" justify="start" v-else-if="!games.data.length">
					<v-col cols="12">
						No games have been created yet.
					</v-col>
				</v-row>

				<!-- Clickable list of games -->
				<v-list id="games" three-line max-height="48vh" v-else>

					<template v-for="(game, index) in games.data">

						<v-list-item
							link
							:key="game.name"
							@click="viewGame(game.id)">

							<v-list-item-content>

								<v-list-item-title>{{ getGameTitleStr(game) }}</v-list-item-title>

								<v-list-item-subtitle v-if="game.author" class="text--primary">
									{{ getGameByLine(game) }}
								</v-list-item-subtitle>

								<v-list-item-subtitle v-if="game.synopsis && game.synopsis.length">
									{{ game.synopsis }}
								</v-list-item-subtitle>

							</v-list-item-content>

						</v-list-item>

						<v-divider v-if="index < games.data.length - 1" :key="index" />

					</template>

				</v-list>

			</template>

		</v-card-text>

		<v-card-actions v-if="!games.loading">

				<v-btn text color="primary" @click="loadGames()">
					Refresh
				</v-btn>

				<v-btn
					text
					color="primary"
					:disabled="games.error ? true : false"
					@click="$emit('navigate', '/admin/games/new');"
				>
					Create Game
				</v-btn>

		</v-card-actions>

	</v-card>

</template>

<style scoped>

	#games {
		background-color: #2a2a2a;
		border: 1px solid #0e0e0e;
		overflow-y: auto;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;
	}

	#games::-webkit-scrollbar {
		width: 11px;
	}

	#games::-webkit-scrollbar-track {
		background: #505050;
	}

	#games::-webkit-scrollbar-thumb {
		background: #d0d0d0;
	}

	.v-card__actions .v-btn {
		font-size: 0.95rem;
		font-weight: bold;
	}

</style>

<script>

	export default {

		mounted: function () {

			this.loadGames();
		},

		data: function () {

			return {

				games: {

					// Set to true whenever we're loading the data via the
					// API.
					loading: true,

					// If an error occurred during loading, that error
					// message will be set here.
					error: null,

					// This is the data that's returned from the API call.
					data: []
				}
			};
		},

		methods: {

			// Returns a string representing the title of a game as it
			// should be displayed in the list.
			getGameTitleStr(game) {

				return game.title ? game.title + ' (' + game.name + ')' : game.name;
			},

			// Returns a string representing the by-line of a game as it
			// should be displayed in the list.
			getGameByLine(game) {

				return game.author ? 'By ' + game.author : '';
			},

			// View the specified game's admin console
			viewGame: function (id) {

				this.$emit('navigate', '/admin/games/' + id);
			},

			// Loads list of games via the API.
			loadGames() {

				this.games.loading = true;
				this.games.error = null;

				let self = this;

				axios
					.get('/admin/api/games')

					.then(response => {

						self.games.data = response.data;
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							self.games.error = error.response.data.error;
						} else {
							self.games.error = error.message;
						}
					})

					.finally(() => {
						self.games.loading = false;
					});
			}
		}
	};

</script>
