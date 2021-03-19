<template>

	<v-card flat>

		<!-- Dialog to confirm destruction or restoration of a dumped game -->
		<v-dialog
			v-model="dialog.show"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="dialog.show = false;"
		>

			<v-card>

				<v-card-title>{{ 'destroy' == dialog.operation ? 'Destroy' : 'Restore' }} Game</v-card-title>

				<v-card-text>
					Are you <strong>*sure*</strong> you want to {{ 'destroy' == dialog.operation ? 'destroy' : 'restore' }}
					this dumped game? This action is permanent and cannot be undone{{
						'destroy' == dialog.operation ? ', and all entities and players will be lost.' : '. If a game with the same id already exists, it will be overwritten, removing any players or other entities that were added since the game was last dumped.'
					}}
				</v-card-text>

				<v-card-actions>

					<v-btn
						text
						color="primary"
						@click="dialog.show = false;"
					>
						Cancel
					</v-btn>

					<v-btn
						text
						color="error"
						@click="proceed()"
					>
						Proceed
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<v-card-title>Dumped Games</v-card-title>

		<v-card-subtitle>
			Manage and restore dumped games.
		</v-card-subtitle>

		<v-card-text>

			<!-- Loading progress -->
			<v-row align="center" justify="start" v-if="status.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<!-- Display this after loading the page (or an error occurred) -->
			<template v-else>

				<message :type="status.messageType" :message="status.message" />

				<v-text-field
					v-model="table.search"
					append-icon="search"
					label="Search any field to narrow down the list"
					single-line
					hide-details
				/>

				<v-data-table
					:headers="table.headers"
					:search="table.search"
					:items="games"
					multi-sort
				>

					<template v-slot:item.created="props">
						{{ showCreated(props.item.created) }}
					</template>

					<template v-slot:item.actions="{ item }">
						<v-icon small @click="confirmRestore(item.id)">restore</v-icon>
						<v-icon small @click="confirmDestroy(item.id)">delete</v-icon>
						<v-icon small @click="expand(item.id)">view_list</v-icon>
					</template>

					<template v-slot:no-data>
						<v-row align="center" justify="start" v-if="!games.length">
							<v-col cols="12">
								<span class="warning">There aren't any dumped games yet.</span>
							</v-col>
						</v-row>
					</template>

				</v-data-table>

			</template>

		</v-card-text>

	</v-card>

</template>


<script>

	import Message from '../ui/Message';

	export default {

		mounted() {

			this.load();
		},

		data: function () {

			return {

				table: {

					search: '',

					headers: [
						{text: 'ID', value: 'id'},
						{text: 'Name', value: 'name'},
						{text: 'Definition', value: 'definition'},
						{text: 'Created', value: 'created'},
						{text: 'Actions', value: 'actions'}
					],
				},

				dialog: {

					// Show the dialog confirming restoration or destruction of a dumped game
					show: false,

					// Will be set to one of the following strings: 'destroy' or 'restore'
					operation: 'destroy',

					// The id of whichever dumped game has been selected
					selectedId: null
				},

				status: {

					// Gets set to false when the page has finished loading
					loading: true,

					// When set to true, the restore and destroy buttons will be disabled
					disableButtons: false,

					// When showing a message, this determines what kind it is (can be one
					// of 'success', 'warning', or 'error'.)
					messageType: 'success',

					// Error message or confirmation to display
					message: ''
				},

				// Games retrieved by load()
				games: []
			};
		},

		methods: {

			// Loads the page
			load() {

				this.status.loading = true;

				axios.get('/admin/api/config')

					.then(response => {

						if (!response.data["state.enabled"]) {
							this.$emit('navigate', '/admin/state');
						}

						return axios.get('/admin/api/dumps');
					})

					.then(response => {

						this.games = response.data;
					})

					.catch(error => {

						this.$emit('navigate', '/admin/state');
					})

					.finally(() => {

						this.status.loading = false;
					});
			},

			// Confirm restoration of a dumped game
			confirmRestore(id) {

				this.dialog.selectedId = id;
				this.dialog.operation = 'restore';
				this.dialog.show = true;
			},

			// Confirm destruction of a dumped game
			confirmDestroy(id) {

				this.dialog.selectedId = id;
				this.dialog.operation = 'destroy';
				this.dialog.show = true;
			},

			// If an operation was confirmed, carry it out
			proceed() {

				let request;

				if ('destroy' == this.dialog.operation) {
					request = axios.delete("/admin/api/dumps/" + this.dialog.selectedId)
				} else {
					request = axios.post("/admin/api/dumps/" + this.dialog.selectedId + '/restore');
				}

				request.then(response => {

						if ('destroy' == this.dialog.operation) {
							this.load();
						}

						this.status.messageType = 'success';
						this.status.message = "Dump " + ('destroy' == this.dialog.operation ? "destroyed." : "restored.");
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							this.status.message = error.message;
						} else {
							this.status.message = 'An unknown error occurred. Please try again.';
						}

						this.status.messageType = 'error';
					})

					.finally(() => {

						this.status.disableButtons = false;

						// Only display message for 5 seconds
						setTimeout(() => {
							this.status.message = '';
						}, 5000);
					});

				this.dialog.show = false;
			},

			// Display the details of a game's dump history
			expand(id) {

				alert('TODO: expand(' + id + ')');
			},

			// Display a string representation of a UNIX timestamp
			showCreated(timestamp) {

				return (new Date(timestamp * 1000)).toString();
			}
		},

		components: {
			'message': Message
		}
	};

</script>
