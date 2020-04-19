<template>

	<v-card>

		<v-card-text>

			<div id="output">

				<!-- Display this while we're waiting to load and connect 
				to the game-->
				<v-container fill-height fluid v-if="!ready">
  					<v-row align="center" justify="center">
						<v-col>
							<p id="connecting" class="text-center">Connecting...</p>
						</v-col>
					</v-row>
				</v-container>

			</div>

			<v-row>

				<!-- This is where the player enters commands -->
				<v-col cols="12">
					<v-text-field
						v-model="command"
						:label="ready ? 'What do you do now?' : ''"
						append-outer-icon="send"
						clear-icon="clear"
						clearable
						:disabled="!ready"
						@keyup.enter="send"
						@click:append-outer="send"
						@click:clear="clear"
					/>
				</v-col>

			</v-row>

		</v-card-text>

	</v-card>

</template>

<style scoped>

	#output {

		background-color: #2a2a2a;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;

		overflow-x: hidden;
		overflow-y: auto;

		hyphens: auto;
		-webkit-hyphens: auto;
		-moz-hyphens: auto;
		-ms-hyphens: auto;
	}

	#output::-webkit-scrollbar {
		width: 11px;
	}

	#output::-webkit-scrollbar-track {
		background: #505050;
	}

	#output::-webkit-scrollbar-thumb {
		background: #d0d0d0;
	}

	@media only screen and (max-height: 550px) {

		#output {
			height: 47vh;
		}
	}

	@media only screen and (min-height: 551px) and (max-height: 699px) {

		#output {
			height: 55vh;
		}
	}

	@media only screen and (min-height: 700px) {

		#output {
			height: 65vh;
		}
	}

	#connecting {
		font-weight: 700;
		font-size: 1.5em;
	}

</style>

<script>

	export default {

		mounted: function () {

			this.loadGame();
		},

		computed: {

			ready: function () {

				// TODO: when we have code to create a player and connect
				// websockets, include those things in this check.
				return this.game.loading ? false : true;
			}
		},

		data: function () {

			return {

				// The text currently displayed in the command input field
				command: '',

				// Game data
				game: {

					// Whether or not we're loading the game's data
					loading: true
				}
			};
		},

		methods: {

			// Loads the game
			loadGame: function () {

				let self = this;

				// TODO: need to implement userland AJAX API first. For now,
				// I'm just mocking this up with a setTimeout so I can see
				// and test how the page will look while things are loading.
				setTimeout(function () {

					self.$store.commit('setTitle', 'Game Title');
					self.game.loading = false;

					/* TODO: if game not found, execute these lines
						this.$store.commit('setError', 'Game not found');
						this.$emit('navigate', '/games', true);
					*/

					// TODO: we'll need to create a player and then
					// websockets before we can enable commands and the
					// display
				}, 4000);
			},

			// Clears the command input field
			clear: function () {

				this.command = '';
			},

			// Sends command to the game server
			send: function () {

				if (this.command.length) {

					// TODO: submit command
					this.clear();
				}
			}
		}
	};

</script>