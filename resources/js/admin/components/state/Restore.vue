<template>

	<v-card flat>

		<v-dialog class="restore"
			v-model="dialog.show"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="dialog.show = false;"
		>

			<v-card>

				<v-card-text class="restore">{{ dialog.message }}</v-card-text>

				<v-card-actions>

					<v-btn class="restore"
						text
						x-large
						color="primary"
						@click="dialog.show = false;"
					>
						OK
					</v-btn>

				</v-card-actions>

			</v-card>

		</v-dialog>

		<v-card-title>Global Restore</v-card-title>
		<v-card-subtitle>Restore the server's global state from disk.</v-card-subtitle>

		<v-card-text>

            <v-row style="font-size: 1.1rem;">
				<v-col xs="12">Clicking the button below will restore all dumped games from disk. <strong>If any games exist with overlapping ids, they will be overwritten. This operation cannot be undone.</strong></v-col>
			</v-row>

            <v-row style="text-align: center;">
				<v-col cols="0" sm="0" lg="3"></v-col>
				<v-col cols="12" sm="12" lg="6">
					<feature-button
						:elevation="1"
						:hideDescription="true"
						:disabled="status.buttonDisabled"
						:title="status.buttonTitle"
						icon="settings_backup_restore"
						@click="restore()"
					/>
				</v-col>
				<v-col cols="0" sm="0" lg="3"></v-col>
			</v-row>

		</v-card-text>

	</v-card>

</template>


<style>

.v-card__text.restore {
	margin-top: 2.5rem;
}

.v-card__text.restore, .v-btn--text.restore {
	font-size: 1.3rem !important;
}

</style>


<script>

	import FeatureButton from '../ui/FeatureButton';

	export default {

		mounted: function () {

			this.load();
		},

		data: function () {

			return {

				dialog: {

					// Setting this to true will display a dialog notifying the
					// user of success or failure
					show: false,

					// This is the message the dialog should display
					message: ''
				},

				status: {

					// Gets set to false when the page has finished loading
					loading: true,

					// Whether or not the button is disabled
					buttonDisabled: false,

					// Title of the feature button
					buttonTitle: 'Restore'
				}
			};
		},

		methods: {

			// Loads the page
			load() {

				this.status.loading = true;

				axios
					.get('/admin/api/config')

					.then(response => {

						if (!response.data["state.enabled"]) {
							this.$emit('navigate', '/admin/state/' + page);
						}
					})

					// If an error occurred, redirect to the main state page,
					// which will display an informative error message.
					.catch(error => {

						this.$emit('navigate', '/admin/state/' + page);
					})

					.finally(() => {
						this.status.loading = false;
					});
			},

			// Make API request to restore the server's state from disk
			restore() {

				this.status.buttonDisabled = true;
				this.status.buttonTitle = 'Restoring...';

				axios
					.post('/admin/api/restore')

					.then(response => {

						this.dialog.message = 'The global restore was successful.';
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							this.dialog.message = error.message;
						} else {
							this.dialog.message = 'An unknown error occurred. Please try again.';
						}
					})

					.finally(() => {
						this.dialog.show = true;
						this.status.buttonDisabled = false;
						this.status.buttonTitle = 'Restore';
					});
			}
		},

		components: {
			FeatureButton
		}
	};

</script>
