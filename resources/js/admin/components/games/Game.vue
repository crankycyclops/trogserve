<template>

	<v-card id="game">

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

		<!-- Display card for loaded game -->
		<template v-else>

			<v-tabs :show-arrows="isMobile" :vertical="!isMobile">

				<v-tab>
					<v-icon left>videogame_asset</v-icon>
					Details
				</v-tab>

				<v-tab>
					<v-icon left>bar_chart</v-icon>
					Statistics
				</v-tab>

				<v-tab>
					<v-icon left>people</v-icon>
					Players
				</v-tab>

				<!-- Details -->
				<v-tab-item>

					<game-details ref="details"
						:loaded="!game.loading"
						:name="game.data.name"
						:definition="game.data.definition"
						:title="game.data.title"
						:author="game.data.author"
						:synopsis="game.data.synopsis"
						:isRunning="game.data.isRunning"
					/>

				</v-tab-item>

				<!-- Statistics -->
				<v-tab-item>

					<game-statistics />

				</v-tab-item>

				<!-- Players -->
				<v-tab-item>

					<game-players />

				</v-tab-item>

			</v-tabs>

		</template>

	</v-card>

</template>

<style scoped>

@media only screen and (min-width: 1264px) {

	#game .v-tab {
		padding: 0 36px 0 20px;
	}
}

</style>

<script>

	import Details from './tabs/Details.vue';
	import Statistics from './tabs/Statistics.vue';
	import Players from './tabs/Players.vue';

	export default {

		mounted: function () {

			this.loadGame();
		},

		computed: {

			// Returns true if we're on a mobile display
			isMobile() {

				return this.$vuetify.breakpoint.mdAndDown;
			},

			getGameTitleStr: function () {
				return this.game.data.title ?
					this.game.data.title + ' (' + this.game.data.name + ')' :
					this.game.data.name
			},

			getGameByLine: function () {
				return this.game.data.author ?
					'By ' + this.game.data.author : '';
			},

			titleMaxLen: function () {return window.titleMaxLen;},
			authorMaxLen: function () {return window.authorMaxLen;},
			synopsisMaxLen: function () {return window.synopsisMaxLen;},

			nameMaxLenMsg: function () {return window.nameMaxLenMsg;},
			titleMaxLenMsg: function () {return window.titleMaxLenMsg;},
			authorMaxLenMsg: function () {return window.authorMaxLenMsg;},
			synopsisMaxLenMsg: function () {return window.synopsisMaxLenMsg;}
		},

		data: function () {

			return {

				// These are the bits of game data we can update
				form: {

					// Set this to true whenever we begin submitting the
					// form. This will deactivate the form until submission
					// is complete (or there's an error.)
					submitting: false,

					// Whether or not to display the form for editing game
					// meta values
					show: false,

					// Syncs with the values entered into the form by the
					// user
					values: {
						title: null,
						author: null,
						synopsis: null
					}
				},

				game: {

					// Set this to true whenever we load the game's data
					loading: true,

					// Game's data that we've loaded via API call
					data: {

						id: null,
						name: null,
						definition: null,
						title: null,
						author: null,
						synopsis: null,
						isRunning: null,

						// Statistical information associated with the game
						statistics: {
							numPlayers: null
						}
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

						// Update the game data
						self.game.data.id = response.data.id;
						self.game.data.name = response.data.name;
						self.game.data.definition = response.data.definition;
						self.game.data.title = response.data.title;
						self.game.data.author = response.data.author;
						self.game.data.synopsis = response.data.synopsis;
						self.game.data.isRunning = response.data.statistics.isRunning;

						self.game.data.statistics.numPlayers = response.data.statistics.numPlayers;
					})

					// We can't load the game, so return to the games list
					// page with an error message at the top.
					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							self.$store.commit('setError', self.capitalize(error.response.data.error));
						} else {
							self.$store.commit('setError', self.capitalize(error.message));
						}

						// Don't emit a navigation event because that's
						// going to clear the error message. Instead, use
						// the router directly.
						self.$router.push('/admin/games');
					})

					.finally(() => {
						self.game.loading = false;
					});
			}
		},

		components: {
			'game-details': Details,
			'game-statistics': Statistics,
			'game-players': Players
		}
	};

</script>
