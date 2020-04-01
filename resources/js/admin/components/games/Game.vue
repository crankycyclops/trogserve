<template>

	<v-card>

		<v-card-title>TODO: game title or name</v-card-title>

		<v-card-subtitle>
			TODO: game by line (if available)
		</v-card-subtitle>

		<v-card-text>
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

	</v-card>

</template>

<script>

	export default {

		mounted: function () {

			this.loadGame();
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
						synopsis: null
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

						// TODO
						console.log(response.data);
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
