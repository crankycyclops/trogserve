<template>

	<v-card>

		<v-card-title>Games</v-card-title>

		<v-card-subtitle>
			Click on a game's ID or name to edit its details or change its state.
		</v-card-subtitle>

		<v-card-text>

			<!-- Loading progress -->
			<v-row align="center" justify="start" v-if="games.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<!-- Display this after games have loaded (or an error occurred) -->
			<template v-else>

				<!-- API call failed -->
				<v-row align="center" justify="start" v-if="games.error">
					<v-col cols="12">
						<span class="error">{{ games.error }}</span>
					</v-col>
				</v-row>

				<!-- Call was successful, but there are no games -->
				<v-row align="center" justify="start" v-else-if="!games.data.length">
					<v-col cols="12">
						No games have been created yet.
					</v-col>
				</v-row>

				<!-- Clickable list of games -->
				<template v-else>

					<v-text-field
						v-model="table.search"
						append-icon="search"
						label="Search any field to narrow down the list"
						single-line
						hide-details
					/>

					<v-data-table
						:headers="table.headers"
						:search="table.search"
						:items="games.data"
						multi-sort
					>

						<template v-slot:item.id="props">
							<div class="clickable" @click="viewGame(props.item.id);">{{ props.item.id }}</div>
						</template>

						<template v-slot:item.name="props">
							<div class="clickable" @click="viewGame(props.item.id);">{{ props.item.name }}</div>
						</template>

					</v-data-table>

				</template>

			</template>

		</v-card-text>

		<v-card-actions v-if="!games.loading">

				<v-btn text color="primary" @click="loadGames()">
					Refresh
				</v-btn>

				<v-btn
					text
					color="primary"
					:disabled="games.error ? true : false"
					@click="$emit('navigate', '/admin/games/new');"
				>
					Create Game
				</v-btn>

		</v-card-actions>

	</v-card>

</template>

<style scoped>

	#games {
		background-color: #2a2a2a;
		border: 1px solid #0e0e0e;
		overflow-y: auto;
		scrollbar-color: #d0d0d0 #505050;
		scrollbar-width: auto;
	}

	#games::-webkit-scrollbar {
		width: 11px;
	}

	#games::-webkit-scrollbar-track {
		background: #505050;
	}

	#games::-webkit-scrollbar-thumb {
		background: #d0d0d0;
	}

	.v-card__actions .v-btn {
		font-size: 0.95rem;
		font-weight: bold;
	}

</style>

<script>

	export default {

		mounted: function () {

			this.loadGames();
		},

		data: function () {

			return {

				table: {

					search: '',

					headers: [
						{text: 'ID', value: 'id'},
						{text: 'Name', value: 'name'},
						{text: 'Definition', value: 'definition'},
						{text: 'Title', value: 'title'},
						{text: 'Author', value: 'author'}
					],
				},

				games: {

					// Set to true whenever we're loading the data via the
					// API.
					loading: true,

					// If an error occurred during loading, that error
					// message will be set here.
					error: null,

					// This is the data that's returned from the API call.
					data: []
				}
			};
		},

		methods: {

			// Returns a string representing the title of a game as it
			// should be displayed in the list.
			getGameTitleStr(game) {

				return game.title ? game.title + ' (' + game.name + ')' : game.name;
			},

			// Returns a string representing the by-line of a game as it
			// should be displayed in the list.
			getGameByLine(game) {

				return game.author ? 'By ' + game.author : '';
			},

			// View the specified game's admin console
			viewGame: function (id) {

				this.$emit('navigate', '/admin/games/' + id);
			},

			// Loads list of games via the API.
			loadGames() {

				this.games.loading = true;
				this.games.error = null;

				axios
					.get('/admin/api/games')

					.then(response => {

						this.games.data = response.data;
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							this.games.error = error.response.data.error;
						} else {
							this.games.error = error.message;
						}
					})

					.finally(() => {
						this.games.loading = false;
					});
			}
		}
	};

</script>
