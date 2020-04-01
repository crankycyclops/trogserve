<template>

	<v-card>

		<v-card-title>Create Game</v-card-title>

		<v-card-subtitle>
			Create a new game.
		</v-card-subtitle>

		<v-card-text>

			<!-- Loading progress -->
			<v-row align="center" justify="start" v-if="definitions.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<!-- Display this after definitions have loaded (or an error occurred) -->
			<template v-else>

				<!-- API call failed -->
				<v-row align="center" justify="start" v-if="definitions.error">
					<v-col cols="12">
						<span class="error">An error occurred: {{ definitions.error }}</span>
					</v-col>
				</v-row>

				<!-- Successfully retrieved definitions, so we can go ahead and create the game -->
				<v-form v-else>

					<v-select
						:items="definitions.data"
						v-model="form.definition"
						label="Definition"
						outlined
					/>

				</v-form>

			</template>

		</v-card-text>

		<v-card-actions>

			<v-btn
				text
				color="primary"
				@click="$router.push('/admin/games')"
			>
				Go Back
			</v-btn>

			<v-btn
				text
				:disabled="definitions.error || definitions.loading ? true : false"
				color="primary"
			>
				Finish
			</v-btn>

		</v-card-actions>

	</v-card>

</template>

<style scoped>

	.v-card__actions .v-btn {
		font-size: 0.95rem;
		font-weight: bold;
	}

</style>

<script>

	export default {

		mounted: function () {

			this.loadDefinitions();
		},

		data: function () {

			return {

				// Controls the form data above
				form: {

					definition: null
				},

				definitions: {

					// Set to true whenever we're loading the list of
					// definitions
					loading: true,

					// If an error occurred while loading the list of
					// available game definitions, it will be set here.
					error: null,

					// Our loaded definitions
					data: []
				}
			};
		},

		methods: {

			// Loads available game definitions via API call.
			loadDefinitions: function () {

				this.definitions.loading = true;
				this.definitions.error = null;

				let self = this;

				axios
					.get('/admin/api/definitions')

					.then(response => {

						if (!response.data.length) {
							self.definitions.error = "No definitions are available. Check trogdord's configuration and try again";
						} else {
							self.definitions.data = response.data;
						}
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							self.definitions.error = error.response.data.error;
						} else {
							self.definitions.error = error.message;
						}
					})

					.finally(() => {
						self.definitions.loading = false;
					});
			}
		}
	};

</script>
