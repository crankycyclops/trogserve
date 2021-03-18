<template>

	<v-card flat>

		<v-card-title>Global Dump</v-card-title>

		<v-card-subtitle>
			Dump the server's global state to disk.
		</v-card-subtitle>

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
						title="Dump"
						icon="file_download"
						@click="dump()"
					/>
				</v-col>
				<v-col cols="0" sm="0" lg="3"></v-col>
			</v-row>

		</v-card-text>

	</v-card>

</template>

<script>

	import FeatureButton from '../ui/FeatureButton';

	export default {

		mounted: function () {

			this.load();
		},

		data: function () {

			return {

				status: {

					// Gets set to false when the page has finished loading
					loading: true,

					// Whether or not the button is disabled
					buttonDisabled: false
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

				// TODO
				this.status.buttonDisabled = true;
			}
		},

		components: {
			FeatureButton
		}
	};

</script>
