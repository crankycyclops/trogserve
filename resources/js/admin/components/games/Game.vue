<template>

	<v-card id="game">

		<progress-bar :show="game.loading" />

		<!-- Display card for loaded game -->
		<template v-if="!game.loading">

			<v-tabs v-model="activeTab" :show-arrows="isMobile" :vertical="!isMobile">

				<v-tab>
					<v-icon left>dvr</v-icon>
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
						:title.sync="game.data.title"
						:author.sync="game.data.author"
						:synopsis.sync="game.data.synopsis"
						:isRunning="game.data.statistics.is_running"
						@navigate="navigate"
						@update:isRunning="updateIsRunning"
						@update="updateGameData"
						@refresh="loadGame(); activeTab = 0;"
					/>

				</v-tab-item>

				<!-- Statistics -->
				<v-tab-item>

					<game-statistics
						:created="game.data.statistics.created"
						:players="game.data.statistics.players"
						@refresh="loadGame(); activeTab = 1;"
					/>

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
			justify-content: left;
		}
	}

</style>

<script>

	import Details from './tabs/Details.vue';
	import Statistics from './tabs/Statistics.vue';
	import Players from './tabs/Players.vue';

	import Progress from '../ui/Progress';
	import RequestMixin from '../../mixins/Request.vue';

	export default {

		mounted() {

			this.loadGame();
		},

		computed: {

			// Returns true if we're on a mobile display
			isMobile() {

				return this.$vuetify.breakpoint.mdAndDown;
			},

			getGameTitleStr() {
				return this.game.data.title ?
					this.game.data.title + ' (' + this.game.data.name + ')' :
					this.game.data.name
			},

			getGameByLine() {
				return this.game.data.author ?
					'By ' + this.game.data.author : '';
			},

			titleMaxLen() {return window.titleMaxLen;},
			authorMaxLen() {return window.authorMaxLen;},
			synopsisMaxLen() {return window.synopsisMaxLen;},

			nameMaxLenMsg() {return window.nameMaxLenMsg;},
			titleMaxLenMsg() {return window.titleMaxLenMsg;},
			authorMaxLenMsg() {return window.authorMaxLenMsg;},
			synopsisMaxLenMsg() {return window.synopsisMaxLenMsg;}
		},

		data() {

			return {

				// Which tab is currently being displayed to the user
				activeTab: 0,

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

						// Statistical information associated with the game
						statistics: {
							created: null,
							is_running: null,
							players: null
						}
					}
				}
			};
		},

		methods: {

			// Because emitted events are only sent to a child's parent and
			// don't go further up the hierarchy, I have to bubble this event
			// up. Meh.
			navigate(uri) {

				this.$emit('navigate', uri);
			},

			// Updates the currently loaded game's data
			updateGameData(updated) {

				Object.keys(updated.payload).forEach(field => {

					if ('undefined' !== typeof this.game.data[field]) {
						this.game.data[field] = updated.payload[field];
					}
				});

				// We have a callback to run after the fields have been
				// updated
				if (updated.callback) {
					updated.callback();
				}
			},

			// Load the specified game
			loadGame() {

				this.game.loading = true;

				axios
					.get('/admin/api/games/' + this.$router.currentRoute.params.id)

					.then(response => {

						// Update game data
						this.updateGameData({payload: response.data});

						// Update game statistics
						this.game.data.statistics.created = response.data.statistics.created;
						this.game.data.statistics.is_running = response.data.statistics.is_running;
						this.game.data.statistics.players = response.data.statistics.players;
					})

					// We can't load the game, so return to the games list
					// page with an error message at the top.
					.catch(error => {

						this.$store.commit('setError', this.getResponseError(error));

						// Don't emit a navigation event because that's
						// going to clear the error message. Instead, use
						// the router directly.
						this.$router.push('/admin/games');
					})

					.finally(() => {
						this.game.loading = false;
					});
			},

			// Update the is_running statistic after the status is toggled by
			// the "Game Details" tab
			updateIsRunning(value) {

				this.game.data.statistics.is_running = value;
			}
		},

		components: {
			'progress-bar': Progress,
			'game-details': Details,
			'game-statistics': Statistics,
			'game-players': Players
		},

		mixins: [
			RequestMixin
		]
	};

</script>
