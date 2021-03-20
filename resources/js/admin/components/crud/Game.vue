<template>

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

</template>

<script>

	import RequestMixin from '../../mixins/Request.vue';

	export default {

        props: {

            id: Number,
            confirmDestroy: Boolean
        },

		data() {

			return {
                //
			};
		},

		methods: {

			// Cancel CRUD operation
			cancel(type) {
                this.$emit('cancel', type, this.id);
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
			}
		},

		mixins: [
			RequestMixin
		]
	};

</script>
