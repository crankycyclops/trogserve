<template>

	<v-card flat>

		<!-- The game details tab contents -->
		<v-card-title>{{ form.show ? 'Edit Details' : getGameTitleStr }}</v-card-title>

		<v-card-subtitle>
			{{ form.show ? "Change basic details such as the game's title and author." : getGameByLine }}
		</v-card-subtitle>

		<v-card-text>

			<message type="error" :message="form.error" />

			<game-crud
				:id="getId()"
				:confirmDestroy="showDestroyDialog"
				@destroy="onDestroy"
				@cancel="onCancelCrud"
				@error="onErrorCrud"
			/>

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

				<v-btn text color="error" @click="showDestroyDialog = true;">
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

	import GameCrud from '../../crud/Game';
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
			}
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

			// Returns the id portion of the route as an integer
			getId() {

				return parseInt(this.$router.currentRoute.params.id);
			},

			// Resets the form to reflect the values associated with the game
			resetForm() {

				this.form.values.title = this.title;
				this.form.values.author = this.author;
				this.form.values.synopsis = this.synopsis;
			},

			// Called when a user cancels a CRUD operation
			onCancelCrud(type, id) {

				switch (type) {

					case 'destroy':
						this.showDestroyDialog = false;
						break;

					default:
						break;
				};
			},

			// Called when a CRUD operation results in an error
			onErrorCrud(type, id, message) {

				this.form.error = message;

				setTimeout(() => {
					this.form.error = '';
				}, 5000);
			},

			// Called when a Game is successfully destroyed
			onDestroy(id) {

				this.$emit('navigate', '/admin/games');
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
			'game-form': GameForm,
			'game-crud': GameCrud
		},

		mixins: [
			RequestMixin
		]
	};

</script>
