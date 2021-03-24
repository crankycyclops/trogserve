<template>

    <div>

        <!-- Dialog to confirm deletion of a game -->
        <v-dialog
            v-model="confirmDestroy"
            overlay-opacity="0.8"
            max-width="500px"
            @keydown.esc="cancelDestroy()"
        >

            <v-card>

                <v-card-title>Destroy Game</v-card-title>

                <v-card-text>
                    Are you <strong>*sure*</strong> you want to destroy this game? This action is permanent and cannot be undone. All entities and players will be lost.
                </v-card-text>

                <v-card-actions>

                    <v-btn
                        text
                        color="primary"
                        @click="cancel('destroy');"
                    >
                        Cancel
                    </v-btn>

                    <v-btn
                        text
                        color="error"
                        @click="destroy();"
                    >
                        Destroy Game
                    </v-btn>

                </v-card-actions>

            </v-card>

        </v-dialog>

        <!-- Form to create or update a game -->
        <template v-if="showForm">

            <!-- If an error occurred during editing or creating, display it above the form -->
            <message type="error" :message="error" />

            <v-row align="center" justify="start">
                <v-col cols="12">
                    <game-form ref="form"
                        :name.sync="form.name"
                        :definition.sync="form.definition"
                        :title.sync="form.title"
                        :author.sync="form.author"
                        :synopsis.sync="form.synopsis"
                        :autostart.sync="form.autostart"
                        :createMode="('undefined' == typeof id)"
                        :definitionList="definitions"
                        :submitting="form.submitting"
                    />
                </v-col>
            </v-row>

        </template>

    </div>

</template>


<script>

	import Message from '../ui/Message';
	import GameForm from '../forms/Game.vue';
	import RequestMixin from '../../mixins/Request.vue';

	export default {

        props: {

            // Id if the game we're operating on (undefined if creating a new game)
            id: Number,

            // If editing, this object should contain the game's current values
            data: Object,

            // Toggles the game delete dialog
            confirmDestroy: Boolean,

            // Toggles the edit/create form
            showForm: Boolean,

            // List of available definition files for the game
            definitions: Array
        },

        watch: {

            data(obj) {

                if ('undefined' != typeof obj) {
                    ['name', 'definition', 'title', 'author', 'synopsis', 'autostart'].forEach(property => {
                        if ('undefined' != typeof obj[property]) {
                            this.form[property] = obj[property];
                        }
                    });
                }
            }
        },

		data() {

			return {

                form: {

					// Set this to true whenever the form is in the process
					// of being submitted. This will result in the form
					// and the buttons being disabled.
					submitting: false,

					// Corresponds to form values above
					name: 'undefined' == typeof data || 'undefined' == typeof data.name ? '' : data.name,
					definition: 'undefined' == typeof data || 'undefined' == typeof data.definition ? '' : data.definition,
					title: 'undefined' == typeof data || 'undefined' == typeof data.title ? '' : data.title,
					author: 'undefined' == typeof data || 'undefined' == typeof data.author ? '' : data.author,
					synopsis: 'undefined' == typeof data || 'undefined' == typeof data.synopsis ? '' : data.synopsis,
					autostart: false, // only used for new games
				},

                // If an error occurs while editing or creating, set this to
                // the message to display it above the form
                error: ''
			};
		},

		methods: {

			// Cancel CRUD operation
			cancel(type) {
                this.$emit('cancel', type, this.id);
			},

            // Calls the API to dump the game
            dump() {

				axios.post('/admin/api/games/' + this.id + '/dump')

					.then(response => {
                        this.$emit('dump', this.id);
					})

					.catch(error => {
                        this.$emit('error', 'dump', this.id, this.getResponseError(error));
					});
            },

			// Call the API to destroy the game
			destroy() {

				axios.delete('/admin/api/games/' + this.id)

					.then(response => {
                        this.$emit('destroy', this.id);
					})

					.catch(error => {
						this.cancel('destroy');
                        this.$emit('error', 'destroy', this.id, this.getResponseError(error));
					});
			},

            // Creates a new game
            create() {

                throw new Error("Creating new games isn't supported yet!");
            },

            // Updates an existing game
            update() {

				let modified = {};

                // Only these three values can be changed for an existing game
				['title', 'author', 'synopsis'].forEach(field => {
                    if (this.data[field] !== this.form[field]) {
						modified[field] = this.form[field];
                    }
				});

				// Nothing actually needs to be updated, so we don't have to
				// make a request :)
				if (!Object.keys(modified).length) {
                    this.$emit('edit', this.id, false);
					return;
				}

				this.form.submitting = true;

				axios.post('/admin/api/games/' + this.id + '/meta', modified)

					// After successful update, reset the form and hide it.
					.then(response => {
						this.$emit('edit', this.id, modified);
					})

					.catch(error => {

                        this.error = this.getResponseError(error);
                        this.$emit('error', 'edit', this.id, this.error);

                        setTimeout(() => {
                            this.error = '';
                        }, 5000);
                    })

					.finally(() => {
						this.form.submitting = false;
					});
            },

            // Called when the form is submitted
            submit() {

				if (!this.$refs.form.validate()) {
                    this.$emit('error', 'validation', this.id);
                    return;
				}

                if ('undefined' == this.id) {
                    this.create();
                } else {
                    this.update();
                }
            }
		},

		components: {
            'message': Message,
			'game-form': GameForm
		},

		mixins: [
			RequestMixin
		]
	};

</script>
