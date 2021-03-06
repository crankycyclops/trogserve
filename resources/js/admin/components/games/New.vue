<template>

	<v-card>

		<v-card-title>Create Game</v-card-title>

		<v-card-subtitle>
			Create a new game.
		</v-card-subtitle>

		<v-card-text>

			<progress-bar :show="definitions.loading" />

			<!-- Display this after definitions have loaded (or an error occurred) -->
			<template v-if="!definitions.loading">

				<message class="message" type="error" :message="definitions.error" />
				<message class="message" type="error" :message="form.error" />

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
				:disabled="definitions.error.length || definitions.loading || form.submitting ? true : false"
				color="primary"
				@click="submit()"
			>
				Finish
			</v-btn>

		</v-card-actions>

	</v-card>

</template>


<style scoped>

.message {
	margin-bottom: 1em;
}

</style>


<script>

	import Message from '../ui/Message';
	import Progress from '../ui/Progress';
	import GameForm from '../forms/Game.vue';
	import RequestMixin from '../../mixins/Request.vue';

	export default {

		mounted() {

			this.loadDefinitions();
		},

		data() {

			return {

				form: {

					// Set this to true whenever the form is in the process
					// of being submitted. This will result in the form
					// and the buttons being disabled.
					submitting: false,

					// If submission of the form results in an error, this
					// will be set to the error message
					error: '',

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
					error: '',

					// Our loaded definitions
					data: []
				}
			};
		},

		methods: {

			// Loads available game definitions via API call.
			loadDefinitions() {

				this.definitions.loading = true;
				this.definitions.error = '';

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
						this.definitions.error = this.getResponseError(error);
					})

					.finally(() => {
						this.definitions.loading = false;
					});
			},

			// Submit the form to create a new game
			submit() {

				this.form.error = '';

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

						setTimeout(() => {
							this.form.error = '';
						}, 5000);
					})

					.finally(() => {
						this.form.submitting = false;
					});
			}
		},

		components: {
			'message': Message,
			'progress-bar': Progress,
			'game-form': GameForm
		},

		mixins: [
			RequestMixin
		]
	};

</script>
