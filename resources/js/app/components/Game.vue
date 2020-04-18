<template>

	<v-card>

		<v-card-text>

			<div id="output"></div>

			<v-row>

				<v-col cols="12">
					<v-text-field
						v-model="command"
						label="What do you do now?"
						append-outer-icon="send"
						clear-icon="clear"
						clearable
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

	#output {
		background-color: #2a2a2a;
		overflow-y: auto;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;
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

</style>

<script>

	export default {

		mounted: function () {

			this.$store.commit('setTitle', 'Game Title');
		},

		data: function () {

			return {

				// The text currently displayed in the command input field
				command: ''
			};
		},

		methods: {

			// Clears the command input field
			clear: function () {

				this.command = '';
			},

			// Sends command to the game server
			send: function () {

				if (this.command.length) {

					// TODO
					this.clear();
				}
			}
		}
	};

</script>
