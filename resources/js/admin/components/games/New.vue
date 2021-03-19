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

				<!-- If submission results in a server side error, it'll
					be displayed here. -->
				<v-row align="center" justify="start" v-if="form.error">
					<v-col cols="12">
						<span class="error">{{ form.error }}</span>
					</v-col>
				</v-row>

				<!-- Successfully retrieved definitions, so we can go ahead and create the game -->
				<game-form ref="form"
					:name.sync="form.name"
					:definition.sync="form.definition"
					:title.sync="form.title"
					:author.sync="form.author"
					:synopsis.sync="form.synopsis"
					:autostart.sync="form.autostart"
					:createMode="true"
					:definitionList="definitions.data"
					:submitting="form.submitting"
				/>

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

	import GameForm from '../forms/Game.vue';
	import RequestMixin from '../../mixins/Request.vue';

	export default {

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
					name: '',
					definition: '',
					title: '',
					author: '',
					synopsis: '',
					autostart: false,
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

				axios
					.get('/admin/api/definitions')

					.then(response => {

						if (!response.data.length) {
							this.definitions.error = "No definitions are available. Check trogdord's configuration and try again";
						} else {
							this.definitions.data = response.data;
						}
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							this.definitions.error = error.response.data.error;
						} else {
							this.definitions.error = error.message;
						}
					})

					.finally(() => {
						this.definitions.loading = false;
					});
			},

			// Submit the form to create a new game
			submit: function () {

				this.form.error = null;

				if (!this.$refs.form.validate()) {
					return false;
				}

				let data = {
					name: this.form.name,
					definition: this.form.definition
				};

				['title', 'author', 'synopsis'].forEach(field => {
					if (field.length) {
						data[field] = this.form[field];
					}
				});

				if (this.form.autostart) {
					data.autostart = this.form.autostart;
				}

				this.form.submitting = true;

				axios.post('/admin/api/games', data)

					// After successful creation of the game, reset the
					// form and view the game's admin console.
					.then(response => {
						this.$refs.form.reset();
						this.$emit('navigate', '/admin/games/' + response.data.id);
					})

					.catch(error => {
						this.form.error = this.getResponseError(error);
					})

					.finally(() => {
						this.form.submitting = false;
					});
			}
		},

		components: {
			'game-form': GameForm
		},

		mixins: [
			RequestMixin
		]
	};

</script>
