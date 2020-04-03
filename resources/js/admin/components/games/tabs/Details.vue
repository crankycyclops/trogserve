<template>

	<v-card flat>

		<v-card-title>{{ form.show ? 'Edit Details' : getGameTitleStr }}</v-card-title>

		<v-card-subtitle>
			{{ form.show ? 'Change basic details such as the game\'s title and author.' : getGameByLine }}
		</v-card-subtitle>

		<v-card-text>

			<!-- Provide an interface for the user to edit basic game details -->
			<v-row align="center" justify="start" v-if="form.show">
				<v-col cols="12">
					<game-form
						:name="name"
						:definition="definition"
						:title.sync="form.values.title"
						:author.sync="form.values.author"
						:synopsis.sync="form.values.synopsis"
						:definitionList="[definition]"
						:submitting="form.submitting"
					/>
				</v-col>
			</v-row>

			<!-- Display details and stats about the game -->
			<template v-else>

				<v-row align="center" justify="start" v-if="synopsis ? true : false">
					<v-col cols="12">
						{{ synopsis }}
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
					@click="editDetails()"
				>
					Edit
				</v-btn>

				<v-btn
					text
					color="error"
					@click="destroy()"
				>
					Destroy
				</v-btn>

			</template>

		</v-card-actions>

	</v-card>

</template>

<script>

	import GameForm from '../../forms/Game.vue';

	export default {

		// The first time the component is mounted, the game data has already
		// been loaded, so make sure we initialize the form.
		mounted: function () {

			this.resetForm();
		},

		props: {

			loaded: {
				type: Boolean
			},

			name: {
				type: String
			},

			definition: {
				type: String
			},

			title: {
				type: String
			},

			author: {
				type: String
			},

			synopsis: {
				type: String
			},

			isRunning: {
				type: Boolean
			}
		},

		computed: {

			getGameTitleStr: function () {
				return this.title ?
					this.title + ' (' + this.name + ')' :
					this.name
			},

			getGameByLine: function () {
				return this.author ?
					'By ' + this.author : '';
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
				}
			};
		},

		watch: {

			// Reset the form whenever a new game is loaded
			loaded: function () {

				this.resetForm();
			}
		},

		methods: {

			// Resets the form to reflect the values associated with the game
			resetForm: function () {

				this.form.values.title = this.title;
				this.form.values.author = this.author;
				this.form.values.synopsis = this.synopsis;
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
			}
		},

		components: {
			'game-form': GameForm
		}
	};

</script>
