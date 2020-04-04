<template>

	<v-card flat>

		<!-- Dialog to confirm deletion of a game -->
		<v-dialog
			v-model="showDestroyDialog"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="cancelDestroy()"
		>

			<v-card>

				<v-card-title>Destroy Game</v-card-title>

				<v-card-text>
					Are you <strong>*sure*</strong> you want to destroy this game? This action is permanent and cannot be undone. All entities and players will be lost.
				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="cancelDestroy()"
					>
						Cancel
					</v-btn>

					<v-btn
						text
						color="error"
						@click="destroy()"
					>
						Destroy Game
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<!-- The game details tab contents -->
		<v-card-title>{{ form.show ? 'Edit Details' : getGameTitleStr }}</v-card-title>

		<v-card-subtitle>
			{{ form.show ? 'Change basic details such as the game\'s title and author.' : getGameByLine }}
		</v-card-subtitle>

		<v-card-text>

			<!-- If submission results in a server side error, it'll be
				displayed here. -->
			<v-row align="center" justify="start" v-if="form.error">
				<v-col cols="12">
					<span class="error">{{ form.error }}</span>
				</v-col>
			</v-row>

			<!-- Provide an interface for the user to edit basic game details -->
			<v-row align="center" justify="start" v-if="form.show">
				<v-col cols="12">
					<game-form ref="form"
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

				<v-row align="center" justify="start">
					<v-col cols="12">
						{{ synopsis ? synopsis : '(No synopsis available)' }}
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
					@click="promptDestroy()"
				>
					Destroy
				</v-btn>

			</template>

		</v-card-actions>

	</v-card>

</template>

<script>

	import GameForm from '../../forms/Game.vue';
	import RequestMixin from '../../../mixins/Request.vue';

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

				// Toggles the "Destroy Game" confirmation dialog
				showDestroyDialog: false,

				// These are the bits of game data we can update
				form: {

					// Set this to true whenever we begin submitting the
					// form. This will deactivate the form until submission
					// is complete (or there's an error.)
					submitting: false,

					// If an error occurs during submission, set it here
					error: null,

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

			// Prompts the user for confirmation before destroying the game
			promptDestroy: function () {

				this.showDestroyDialog = true;
			},

			// During confirmation, user decided not to destroy the game
			cancelDestroy: function () {

				this.showDestroyDialog = false;
			},

			// Call the API to destroy the game
			destroy: function () {

				let self = this;

				axios.delete('/admin/api/games/' + this.$router.currentRoute.params.id) 

					// After successful update, reset the form and hide it.
					.then(response => {
						self.showDestroyDialog = false;
						self.$emit('navigate', '/admin/games');
					})

					.catch(error => {
						self.form.error = self.getResponseError(error);
					});
			},

			// Provide an interface for the user to edit the game's details
			editDetails: function () {

				this.form.show = true;
			},

			// Submit updated game details
			submitDetails: function () {

				this.form.error = null;

				if (!this.$refs.form.validate()) {
					return false;
				}

				let self = this;
				let data = {};

				['title', 'author', 'synopsis'].forEach(function(field) {
					if (self[field] !== self.form.values[field]) {
						data[field] = self.form.values[field];
					}
				});

				// Nothing actually needs to be updated, so we don't have to
				// make a request :)
				if (!Object.keys(data).length) {
					self.form.show = false;
					return;
				}

				this.form.submitting = true;

				axios.post('/admin/api/games/' + this.$router.currentRoute.params.id + '/meta', data)

					// After successful update, reset the form and hide it.
					.then(response => {
						self.$emit('update', {payload: data, callback: function () {
							self.$nextTick(() => {
								self.resetForm();
								self.form.show = false;
							});
						}});
					})

					.catch(error => {
						self.form.error = self.getResponseError(error);
					})

					.finally(() => {
						self.form.submitting = false;
					});
			},

			cancelEditDetails: function () {

				this.resetForm();
				this.form.show = false;
			}
		},

		components: {
			'game-form': GameForm
		},

		mixins: [
			RequestMixin
		]
	};

</script>
