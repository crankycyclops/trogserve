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

			<v-card-title>{{ form.show ? 'Edit Details' : getGameTitleStr }}</v-card-title>

			<v-card-subtitle>
				{{ form.show ? 'Change basic details such as the game\'s title and author.' : getGameByLine }}
			</v-card-subtitle>

			<v-card-text>

				<!-- Provide an interface for the user to edit basic game details -->
				<v-row align="center" justify="start" v-if="form.show">
					<v-col cols="12">
						<game-form
							:name="game.data.name"
							:definition="game.data.definition"
							:title.sync="form.values.title"
							:author.sync="form.values.author"
							:synopsis.sync="form.values.synopsis"
							:definitionList="[game.data.definition]"
							:submitting="form.submitting"
						/>
					</v-col>
				</v-row>

				<!-- Display details and stats about the game -->
				<template v-else>

					<v-row align="center" justify="start" v-if="game.data.synopsis ? true : false">
						<v-col cols="12">
							{{ game.data.synopsis }}
						</v-col>
					</v-row>

					<v-row align="center" justify="start">
						<v-col cols="12">
							TODO: statistics about players and other game-specific stuff
						</v-col>
					</v-row>

				</template>

			</v-card-text>

			<v-card-actions>

				<template v-if="form.show">

					<v-btn
						text
						color="primary"
						@click="cancelEditDetails()"
					>
						Cancel
					</v-btn>

					<v-btn
						text
						color="primary"
						@click="submitDetails()"
					>
						Finish
					</v-btn>

				</template>

				<template v-else>

					<v-btn
						text
						color="primary"
						@click="$emit('navigate', '/admin/games')"
					>
						Go Back
					</v-btn>

					<v-btn
						text
						color="primary"
						@click="editDetails()"
					>
						Edit Details
					</v-btn>

					<v-btn
						text
						color="error"
						@click="destroy()"
					>
						Destroy Game
					</v-btn>

				</template>

			</v-card-actions>

		</template>

	</v-card>

</template>

<script>

	import GameForm from '../forms/Game.vue';

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

			// Resets the form to reflect the values associated with the game
			resetForm: function () {

				this.form.values.title = this.game.data.title;
				this.form.values.author = this.game.data.author;
				this.form.values.synopsis = this.game.data.synopsis;
			},

			// Load the specified game
			loadGame: function () {

				let self = this;
				this.game.loading = true;

				axios
					.get('/admin/api/games/' + this.$router.currentRoute.params.id)

					.then(response => {

						// Update the game data
						self.game.data.name = response.data.name;
						self.game.data.definition = response.data.definition;
						self.game.data.title = response.data.title;
						self.game.data.author = response.data.author;
						self.game.data.synopsis = response.data.synopsis;
						self.game.data.isRunning = response.data.statistics.isRunning;

						self.game.data.statistics.numPlayers = response.data.statistics.numPlayers;

						// Update the form data (which might be out of
						// sync with the game data if we're updating any
						// fields.)
						self.resetForm();
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
			},

			// Destroy the game
			destroy: function () {

				// TODO: replace with dialog and actually destroy
				alert('Are you sure you want to destroy this game? This action is permanent and cannot be undone.');
				this.$emit('navigate', '/admin/games');
			},

			// Provide an interface for the user to edit the game's details
			editDetails: function () {

				this.form.show = true;
			},

			// Submit updated game details
			submitDetails: function () {

				// TODO: after update, change details saved in game.data!
				this.form.show = false;
			},

			cancelEditDetails: function () {

				this.resetForm();
				this.form.show = false;
			},

			// Start or stop the game
			toggleStart: function () {

				alert('TODO: toggle start');
			}
		},

		components: {
			'game-form': GameForm
		}
	};

</script>
