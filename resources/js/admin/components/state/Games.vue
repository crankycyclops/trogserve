<template>

	<v-card flat>

		<v-card-title>Dumped Games</v-card-title>

		<v-card-subtitle>
			Manage and restore dumped games.
		</v-card-subtitle>

		<v-card-text>

			<!-- Loading progress -->
			<v-row align="center" justify="start" v-if="status.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<!-- Display this after loading the page (or an error occurred) -->
			<template v-else>

				<v-row align="center" justify="start" v-if="status.error">
					<v-col cols="12">
						<span class="error">Unable to connect to trogdord.</span>
					</v-col>
				</v-row>

				<template v-else>

					<!-- Call was successful, but there are no dumped games -->
					<v-row align="center" justify="start" v-if="!games.length">
						<v-col cols="12">
							<span class="warning">There aren't any dumped games yet.</span>
						</v-col>
					</v-row>

					<!-- Clickable list of dumped games -->
					<v-list id="games" three-line max-height="48vh" v-else>

						<template v-for="(game, index) in games">

							<v-list-item link :key="game.name" @click="expand(game.id)">

								<v-list-item-content>

									<v-list-item-title>{{ game.name }} ({{ game.definition }})</v-list-item-title>
									<v-list-item-subtitle><strong>Created:</strong> {{ showCreated(game.created) }}</v-list-item-subtitle>

								</v-list-item-content>

								<v-list-item-action>

									<v-btn
										text
										color="primary"
										@click.stop="restore(game.id)"
									>
										Restore
									</v-btn>

									<v-btn
										text
										color="error"
										@click.stop="destroy(game.id)"
									>
										Destroy
									</v-btn>

								</v-list-item-action>

							</v-list-item>

							<v-divider v-if="index < games.length - 1" :key="index" />

						</template>

					</v-list>

				</template>

			</template>

		</v-card-text>

	</v-card>

</template>


<style>

	#games {
		background-color: #2a2a2a;
		border: 1px solid #0e0e0e;
		overflow-y: auto;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;
	}

</style>


<script>

	export default {

		mounted() {

			this.load();
		},

		data: function () {

			return {

				status: {

					// Gets set to false when the page has finished loading
					loading: true
				},

				// Games retrieved by load()
				games: []
			};
		},

		methods: {

			// Loads the page
			load() {

				this.status.loading = true;

				axios.get('/admin/api/config')

					.then(response => {

						if (!response.data["state.enabled"]) {
							this.$emit('navigate', '/admin/state');
						}

						return axios.get('/admin/api/dumps');
					})

					.then(response => {

						this.games = response.data;
					})

					.catch(error => {

						this.$emit('navigate', '/admin/state');
					})

					.finally(() => {

						this.status.loading = false;
					});
			},

			// Restore a dumped game
			restore(id) {

				alert('TODO: restore(' + id + ')');
			},

			// Destroy a dumped game
			destroy(id) {

				alert('TODO: destroy(' + id + ')');
			},

			// Display the details of a game's dump history
			expand(id) {

				alert('TODO: expand(' + id + ')');
			},

			// Display a string representation of a UNIX timestamp
			showCreated(timestamp) {

				let date = new Date(timestamp * 1000);
				let timeStr = date.getHours() + ':' +
					('0' + date.getMinutes()).slice(-2) + ':' +
					('0' + date.getSeconds()).slice(-2);
				let dateStr = 'TODO';

				return dateStr + ' ' + timeStr;
			}
		}
	};

</script>
