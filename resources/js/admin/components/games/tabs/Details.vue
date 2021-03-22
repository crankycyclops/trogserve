<template>

	<v-card flat>

		<!-- The game details tab contents -->
		<v-card-title>{{ form.show ? 'Edit Details' : getGameTitleStr }}</v-card-title>

		<v-card-subtitle>
			{{ form.show ? "Change basic details such as the game's title and author." : getGameByLine }}
		</v-card-subtitle>

		<v-card-text>

			<game-crud ref="crud"
				:id="getId()"
				:data="form.values"
				:definitions="form.definitions"
				:confirmDestroy="showDestroyDialog"
				:showForm="form.show"
				@destroy="onDestroy"
				@edit="onEdit"
				@cancel="onCancelCrud"
				@error="onErrorCrud"
			/>

			<template v-if="!form.show">

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

				<v-btn text color="primary" @click="form.show = false;">
					Cancel
				</v-btn>

				<v-btn text color="primary" @click="$refs.crud.submit();">
					Finish
				</v-btn>

			</template>

			<template v-else>

				<v-btn text color="primary" @click="$emit('refresh');">
					Refresh
				</v-btn>

				<v-btn text color="primary" @click="edit();">
					Edit
				</v-btn>

				<v-btn text color="primary" @click="dump();">
					Dump
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

					// Whether or not to display the form for editing game
					// meta values
					show: false,

					// Used to initialize the read-only drop-down for the game editing form
					definitions: [this.definition],

					// Populates the game editing form
					values: {}
				}
			};
		},

		methods: {

			// Returns the id portion of the route as an integer
			getId() {

				return parseInt(this.$router.currentRoute.params.id);
			},

			// Dump the game
			dump() {

				alert('TODO: dump()');
			},

			// Display the edit game details form
			edit() {

				this.form.values = {
					name: this.name,
					definition: this.definition,
					title: this.title,
					author: this.author,
					synopsis: this.synopsis
				};

				this.form.show = true;
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

				// If we're editing, we'll let the component display the error
				// above the form
				if ('edit' != type) {

					this.form.error = message;

					setTimeout(() => {
						this.form.error = '';
					}, 5000);
				}
			},

			// Called when a Game is successfully destroyed
			onDestroy(id) {

				this.$emit('navigate', '/admin/games');
			},

			// Called when a Game is successfully edited
			onEdit(id, changed) {

				Object.keys(changed).forEach(key => {
					console.log('update:' + key);
					this.$emit('update:' + key, changed[key]);
				});

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
