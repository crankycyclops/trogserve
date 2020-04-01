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
						<span class="error">{{ definitions.error }}</span>
					</v-col>
				</v-row>

				<!-- Successfully retrieved definitions, so we can go ahead and create the game -->
				<v-form ref="form" v-else>

					<!-- If submission results in a server side error, it'll
						be displayed here. -->
					<v-row align="center" justify="start" v-if="form.error">
						<v-col cols="12">
							<span class="error">{{ form.error }}</span>
						</v-col>
					</v-row>

					<v-text-field
						v-model.trim="form.name"
						:counter="nameMaxLen"
						:rules="form.validation.name"
						:disabled="form.submitting"
						label="Name"
						required
						outlined
					></v-text-field>

					<v-select
						v-model="form.definition"
						:items="definitions.data"
						:rules="form.validation.definition"
						:disabled="form.submitting"
						label="Definition"
						required
						outlined
					/>

					<v-text-field
						v-model.trim="form.title"
						:counter="titleMaxLen"
						:rules="form.validation.title"
						:disabled="form.submitting"
						label="Title"
						outlined
					/>

					<v-text-field
						v-model.trim="form.author"
						:counter="authorMaxLen"
						:rules="form.validation.author"
						:disabled="form.submitting"
						label="Author"
						outlined
					/>

					<v-textarea
						v-model.trim="form.synopsis"
						:counter="synopsisMaxLen"
						:rules="form.validation.synopsis"
						:disabled="form.submitting"
						label="Synopsis"
						outlined
					/>

					<v-switch
						v-model="form.autostart"
						:disabled="form.submitting"
						class="ma-1"
						label="Autostart the game"
					/>

					<!-- Progress bar indicates when a game is being created -->
					<v-row align="center" justify="start" v-if="form.submitting">
						<v-col cols="12">
							<v-progress-linear :active="true" :indeterminate="true" />
						</v-col>
					</v-row>

				</v-form>

			</template>

		</v-card-text>

		<v-card-actions>

			<v-btn
				text
				:disabled="form.submitting"
				color="primary"
				@click="$emit('navigate', '/admin/games')"
			>
				Go Back
			</v-btn>

			<v-btn
				text
				:disabled="definitions.error || definitions.loading || form.submitting ? true : false"
				color="primary"
				@click="submit()"
			>
				Finish
			</v-btn>

		</v-card-actions>

	</v-card>

</template>

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

					// Set this to true whenever the form is in the process
					// of being submitted. This will result in the form
					// and the buttons being disabled.
					submitting: false,

					// If submission of the form results in an error, this
					// will be set to the error message
					error: null,

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

			// Reset the game creation form
			reset: function () {

				this.$refs.form.reset();
				this.$refs.form.resetValidation();
			},

			// Submit the form to create a new game
			submit: function () {

				this.form.error = null;

				if (!this.$refs.form.validate()) {
					return false;
				}

				let self = this;

				let data = {
					name: this.form.name,
					definition: this.form.definition
				};

				['title', 'author', 'synopsis'].forEach(function(field) {
					if (field.length) {
						data[field] = self.form[field];
					}
				});

				this.form.submitting = true;

				axios.post('/admin/api/games', data)

					// After successful creation of the game, reset the
					// form and view the game's admin console.
					.then(response => {
						self.reset();
						self.$emit('navigate', '/admin/games/' + response.data.id);
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							self.form.error = error.response.data.error;
						} else {
							self.form.error = error.message;
						}
					})

					.finally(() => {
						self.form.submitting = false;
					});
			}
		}
	};

</script>
