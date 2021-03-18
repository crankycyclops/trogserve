<template>

	<v-card flat>

		<v-dialog class="dump"
			v-model="dialog.show"
			overlay-opacity="0.8"
			max-width="500px"
			@keydown.esc="dialog.show = false;"
		>

			<v-card>

				<v-card-text class="dump">{{ dialog.message }}</v-card-text>

				<v-card-actions>

					<v-btn class="dump"
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

		<v-card-title>Global Dump</v-card-title>
		<v-card-subtitle>Dump the server's global state to disk.</v-card-subtitle>

		<v-card-text>

            <v-row style="font-size: 1.1rem;">
				<v-col xs="12">Clicking the button below will write the state of all existing games to disk. They can later be restored one by one, or by doing a single global restore. No data will be lost or overwritten by this operation.</v-col>
			</v-row>

            <v-row style="text-align: center;">
				<v-col cols="0" sm="0" lg="3"></v-col>
				<v-col cols="12" sm="12" lg="6">
					<feature-button
						:elevation="1"
						:hideDescription="true"
						:disabled="status.buttonDisabled"
						:title="status.buttonTitle"
						icon="file_download"
						@click="dump()"
					/>
				</v-col>
				<v-col cols="0" sm="0" lg="3"></v-col>
			</v-row>

		</v-card-text>

	</v-card>

</template>


<style>

.v-card__text.dump {
	margin-top: 2.5rem;
}

.v-card__text.dump, .v-btn--text.dump {
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
					buttonTitle: 'Dump'
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

			// Make API request to dump the server's state to disk
			dump() {

				this.status.buttonDisabled = true;
				this.status.buttonTitle = 'Dumping...';

				axios
					.post('/admin/api/dump')

					.then(response => {

						this.dialog.message = 'The dump was successful.';
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
						this.status.buttonTitle = 'Dump';
					});
			}
		},

		components: {
			FeatureButton
		}
	};

</script>
