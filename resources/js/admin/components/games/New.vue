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
				<v-form ref="form" v-else>

					<v-text-field
						v-model.trim="form.name"
						:counter="nameMaxLen"
						:rules="form.validation.name"
						label="Name"
						required
						outlined
					></v-text-field>

					<v-select
						v-model="form.definition"
						:items="definitions.data"
						:rules="form.validation.definition"
						label="Definition"
						required
						outlined
					/>

					<v-text-field
						v-model.trim="form.title"
						:counter="titleMaxLen"
						:rules="form.validation.title"
						label="Title"
						outlined
					/>

					<v-text-field
						v-model.trim="form.author"
						:counter="authorMaxLen"
						:rules="form.validation.author"
						label="Author"
						outlined
					/>

					<v-textarea
						v-model.trim="form.synopsis"
						:counter="synopsisMaxLen"
						:rules="form.validation.synopsis"
						label="Synopsis"
						outlined
					/>

					<v-switch
						v-model="form.autostart"
						class="ma-1"
						label="Autostart the game"
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
				@click="submit()"
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

		computed: {

			nameMaxLen: function () {return window.nameMaxLen;},
			titleMaxLen: function () {return window.titleMaxLen;},
			authorMaxLen: function () {return window.authorMaxLen;},
			synopsisMaxLen: function () {return window.synopsisMaxLen;},

			nameMaxLenMsg: function () {return window.nameMaxLenMsg;},
			titleMaxLenMsg: function () {return window.titleMaxLenMsg;},
			authorMaxLenMsg: function () {return window.authorMaxLenMsg;},
			synopsisMaxLenMsg: function () {return window.synopsisMaxLenMsg;}
		},

		mounted: function () {

			this.loadDefinitions();
		},

		data: function () {

			return {

				form: {

					// Corresponds to form values above
					name: null,
					definition: null,
					title: null,
					author: null,
					synopsis: null,
					autostart: null,

					// Validation rules for the form fields above
					validation: {

						name: [
							v => !!v || "You must give the game a name",
							v => (v || '').length <= this.nameMaxLen || this.nameMaxLenMsg
						],

						definition: [
							v => !!v || 'You must choose a game definition',
						],

						title:[
							v => (v || '').length <= this.titleMaxLen || this.titleMaxLenMsg
						],

						author: [
							v => (v || '').length <= this.authorMaxLen || this.authorMaxLenMsg
						],

						synopsis: [
							v => (v || '').length <= this.synopsisMaxLen || this.synopsisMaxLenMsg
						]
					}
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
			},


			submit: function () {

				let self = this;

				if (!this.$refs.form.validate()) {
					return false;
				}

				let data = {
					name: this.form.name,
					definition: this.form.definition
				};

				['title', 'author', 'synopsis'].forEach(function(field) {
					if (field.length) {
						data[field] = self.form[field];
					}
				});

				axios.post('/admin/api/games', data);
				// TODO
			}
		}
	};

</script>
