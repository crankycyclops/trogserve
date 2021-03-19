<template>

	<v-card flat>

		<v-card-title>Dump &amp; Restore</v-card-title>

		<v-card-subtitle>
			Dump or restore the server's state to/from disk. These features allow the server's state to survive crashes and restarts.
		</v-card-subtitle>

		<v-card-text>

			<progress-bar :show="status.loading" />

			<!-- Display this after loading the page (or an error occurred) -->
			<template v-if="!status.loading">

				<message v-if="status.error" type="error" message="Unable to connect to trogdord" />

				<template v-else>

					<message v-if="!stateEnabled" type="error" message='State was disabled in trogdord.ini. To enable it and activate this panel, set "state.enabled" to "true," restart the server, and click "Refresh."' />

					<template v-else>

						<v-row class="features">

							<!-- In Vuetify 2.x, cols takes the place of xs. Why the documentation still
							references it is beyond me. See: https://github.com/vuetifyjs/vuetify/issues/8997 -->
							<v-col style="flex-direction: column" cols="12" sm="12" md="6" lg="4" xl="3">
								<feature-button
									:elevation="1"
									title="Dump"
									description="Dump the server's global state to disk."
									icon="file_download"
									@click="navigate('dump');"
								/>
							</v-col>

							<v-col style="flex-direction: column" cols="12" sm="12" md="6" lg="4" xl="3">
								<feature-button
									:elevation="1"
									title="Restore"
									description="Restore the server's global state from disk."
									icon="restore"
									@click="navigate('restore');"
								/>
							</v-col>

							<v-col style="flex-direction: column" cols="12" sm="12" md="6" lg="4" xl="3">
								<feature-button
									:elevation="1"
									title="Games"
									description="Manage one or more dumped games."
									icon="videogame_asset"
									@click="navigate('games');"
								/>
							</v-col>

						</v-row>

					</template>

				</template>

			</template>

		</v-card-text>

		<v-card-actions v-if="!status.loading && (status.error || !stateEnabled)">

				<v-btn text color="primary" @click="load()">
					Refresh
				</v-btn>

		</v-card-actions>

	</v-card>

</template>


<style scoped>

.features {
	padding-top: 15px;
}

</style>


<script>

	import Progress from './ui/Progress';
	import Message from './ui/Message';
	import FeatureButton from './ui/FeatureButton';

	export default {

		mounted() {

			this.load();
		},

		data() {

			return {

				status: {

					// Gets set to false when the page has finished loading
					loading: true,

					// Gets set to true if there was an error loading the page
					error: false
				},

				// Gets set to true when the server supports state
				stateEnabled: false
			};
		},

		methods: {

			// Loads the page
			load() {

				this.status.loading = true;

				axios.get('/admin/api/config')

					.then(response => {

						this.stateEnabled = response.data["state.enabled"];
						this.status.error = false;
					})

					.catch(error => {

						this.status.error = true;
					})

					.finally(() => {
						this.status.loading = false;
					});
			},

			// Navigate to the desired state feature
			navigate(page) {

				this.$emit('navigate', '/admin/state/' + page);
			}
		},

		components: {
			'message': Message,
			'progress-bar': Progress,
			'feature-button': FeatureButton
		}
	};

</script>
