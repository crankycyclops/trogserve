<template>

	<v-card>

		<!-- Loading progress -->
		<template v-if="game.loading">

			<v-card-title>Fetching Details&hellip;</v-card-title>

			<v-card-text>

				<v-row align="center" justify="start">
					<v-col cols="12">
						<v-progress-linear :active="true" :indeterminate="true" />
					</v-col>
				</v-row>

			</v-card-text>

		</template>

		<!-- Display details of loaded game -->
		<template v-else>

			<v-card-title>{{ getGameTitleStr }}</v-card-title>

			<v-card-subtitle>
				{{ getGameByLine }}
			</v-card-subtitle>

			<v-card-text>

				<v-row align="center" justify="start" v-if="game.data.synopsis ? true : false">
					<v-col cols="12">
						{{ game.data.synopsis }}
					</v-col>
				</v-row>

				TODO

			</v-card-text>

			<v-card-actions>

				<v-btn
					text
					color="primary"
					@click="$emit('navigate', '/admin/games')"
				>
					Browse Games
				</v-btn>

				<v-btn
					text
					color="error"
					@click="destroy()"
				>
					Destroy
				</v-btn>

			</v-card-actions>

		</template>

	</v-card>

</template>

<script>

	export default {

		mounted: function () {

			this.loadGame();
		},

		computed: {

			getGameTitleStr: function () {
				return this.game.data.title ?
					this.game.data.title + ' (' + this.game.data.name + ')' :
					this.game.data.name
			},

			getGameByLine: function () {
				return this.game.data.author ?
					'By ' + this.game.data.author : '';
			}
		},

		data: function () {

			return {

				game: {

					// Set this to true whenever we load the game's data
					loading: true,

					// Game's data that we've loaded via API call
					data: {
						name: null,
						definition: null,
						title: null,
						author: null,
						synopsis: null,
						isRunning: null
					}
				}
			};
		},

		methods: {

			// Capitalize the first letter of a string (used primarily for
			// API error messages that are all lowercase.)
			capitalize: string => string.charAt(0).toUpperCase() + string.substring(1),

			// Load the specified game
			loadGame: function () {

				let self = this;
				this.game.loading = true;

				axios
					.get('/admin/api/games/' + this.$router.currentRoute.params.id)

					.then(response => {

						this.game.data.name = response.data.name;
						// this.game.data.definition = response.data.definition;
						this.game.data.title = response.data.title;
						this.game.data.author = response.data.author;
						this.game.data.synopsis = response.data.synopsis;
						this.game.data.isRunning = response.data.isRunning;
					})

					// We can't load the game, so return to the games list
					// page with an error message at the top.
					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							this.$store.commit('setError', this.capitalize(error.response.data.error));
						} else {
							this.$store.commit('setError', this.capitalize(error.message));
						}

						// Don't emit a navigation event because that's
						// going to clear the error message. Instead, use
						// the router directly.
						this.$router.push('/admin/games');
					})

					.finally(() => {
						self.game.loading = false;
					});
			},

			// Destroy the game
			destroy: function () {

				// TODO: replace with dialog and actually destroy
				alert('Are you sure you want to destroy this game? This action is permanent and cannot be undone.');
				this.$emit('navigate', '/admin/games');
			}
		}
	};

</script>
