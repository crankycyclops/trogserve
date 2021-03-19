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

			<message type="error" :message="form.error" />

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

			<template v-else>

				<!-- Details about the game -->
				<v-row align="center" justify="start">
					<v-col cols="12">
						{{ synopsis ? synopsis : '(No synopsis available)' }}
					</v-col>
				</v-row>

				<!-- Start or stop the game -->
				<v-row align="center" justify="start">

					<v-col cols="12">

						<div id="toggleStartDiv">
							<v-switch
								:input-value="isRunning"
								:key="toggleStartData.state"
								:class="isRunning ? 'started' : 'stopped'"
								:label="isRunning ? 'Started' : 'Stopped'"
								:disabled="!toggleStartData.enable"
								@change="toggleStart"
							/>
						</div>

						<div id="toggleStartDesc">
							{{ isRunning ? toggleStartData.startedDesc : toggleStartData.stoppedDesc }}
						</div>

					</v-col>

				</v-row>

				<message type="error" :message="toggleStartData.error" />

			</template>

		</v-card-text>

		<v-card-actions>

			<template v-if="form.show">

				<v-btn text color="primary" @click="cancelEditDetails()">
					Cancel
				</v-btn>

				<v-btn text color="primary" @click="submitDetails()">
					Finish
				</v-btn>

			</template>

			<template v-else>

				<v-btn text color="primary" @click="$emit('refresh')">
					Refresh
				</v-btn>

				<v-btn text color="primary" @click="editDetails()">
					Edit
				</v-btn>

				<v-btn text color="error" @click="promptDestroy()">
					Destroy
				</v-btn>

			</template>

		</v-card-actions>

	</v-card>

</template>

<style>

	/* I have to use !important because of poor design decisions on Vuetify's
	part :( */
	.started label {
		color: #00b300 !important;
		margin-left: 9px;
	}

	.stopped label {
		color: #ff0000 !important;
		margin-left: 9px;
	}

</style>

<script>

	import Message from '../../ui/Message';
	import GameForm from '../../forms/Game.vue';
	import RequestMixin from '../../../mixins/Request.vue';

	export default {

		// The first time the component is mounted, the game data has already
		// been loaded, so make sure we initialize the form.
		mounted() {

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

			getGameTitleStr() {
				return this.title ?
					this.title + ' (' + this.name + ')' :
					this.name
			},

			getGameByLine() {
				return this.author ?
					'By ' + this.author : '';
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

				// Toggles the "Destroy Game" confirmation dialog
				showDestroyDialog: false,

				toggleStartData: {

					// This is how we force the toggle button to re-render
					// if there's an error during the request to start or
					// stop the game
					state: this.isRunning,

					// Disable the start/stop switch during API requests
					enable: true,

					// The last error that occurred when attempting to start
					// or stop the game
					error: '',

					// Description of the started state
					startedDesc: 'The timer is running and the game is accepting player commands.',

					// Description of the stopped state
					stoppedDesc: 'The timer is stopped and the game is not accepting player commands.',
				},

				// These are the bits of game data we can update
				form: {

					// Set this to true whenever we begin submitting the
					// form. This will deactivate the form until submission
					// is complete (or there's an error.)
					submitting: false,

					// If an error occurs during submission, set it here
					error: '',

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
			loaded() {

				this.resetForm();
			}
		},

		methods: {

			// Resets the form to reflect the values associated with the game
			resetForm() {

				this.form.values.title = this.title;
				this.form.values.author = this.author;
				this.form.values.synopsis = this.synopsis;
			},

			// Prompts the user for confirmation before destroying the game
			promptDestroy() {

				this.showDestroyDialog = true;
			},

			// During confirmation, user decided not to destroy the game
			cancelDestroy() {

				this.showDestroyDialog = false;
			},

			// Call the API to destroy the game
			destroy() {

				axios.delete('/admin/api/games/' + this.$router.currentRoute.params.id) 

					// After successful update, reset the form and hide it.
					.then(response => {
						this.showDestroyDialog = false;
						this.$emit('navigate', '/admin/games');
					})

					.catch(error => {

						this.form.error = this.getResponseError(error);

						setTimeout(() => {
							this.form.error = '';
						}, 5000);

						this.cancelDestroy();
					});
			},

			// Provide an interface for the user to edit the game's details
			editDetails() {

				this.form.show = true;
			},

			// Submit updated game details
			submitDetails() {

				this.form.error = '';

				if (!this.$refs.form.validate()) {
					return false;
				}

				let data = {};

				['title', 'author', 'synopsis'].forEach(field => {
					if (this[field] !== this.form.values[field]) {
						data[field] = this.form.values[field];
					}
				});

				// Nothing actually needs to be updated, so we don't have to
				// make a request :)
				if (!Object.keys(data).length) {
					this.form.show = false;
					return;
				}

				this.form.submitting = true;

				axios.post('/admin/api/games/' + this.$router.currentRoute.params.id + '/meta', data)

					// After successful update, reset the form and hide it.
					.then(response => {
						this.$emit('update', {payload: data, callback: () => {
							this.$nextTick(() => {
								this.resetForm();
								this.form.show = false;
							});
						}});
					})

					.catch(error => {

						this.form.error = this.getResponseError(error);

						setTimeout(() => {
							this.form.error = '';
						}, 5000);
					})

					.finally(() => {
						this.form.submitting = false;
					});
			},

			// The user started editing the game's details but decided to cancel
			cancelEditDetails() {

				this.resetForm();
				this.form.show = false;
			},

			// Starts and stops the game
			toggleStart(value) {

				let uriBase = '/admin/api/games/' + this.$router.currentRoute.params.id;

				this.toggleStartData.error = '';
				this.toggleStartData.enable = false;
				this.toggleStartData.state = value;

				axios.get(uriBase + (value ? '/start' : '/stop' ))

					// After successful update, notify the parent to update
					// the game's statistic
					.then(response => {
						this.$emit('update:isRunning', value);
					})

					.catch(error => {

						this.toggleStartData.state = this.isRunning;

						setTimeout(() => {
							this.toggleStartData.error = '';
						}, 5000);

						this.toggleStartData.error = this.getResponseError(error);
					})

					.finally(() => {
						this.toggleStartData.enable = true;
					});
			}
		},

		components: {
			'message': Message,
			'game-form': GameForm
		},

		mixins: [
			RequestMixin
		]
	};

</script>
