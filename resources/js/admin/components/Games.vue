<template>

	<v-card>

		<v-card-title>Games</v-card-title>

		<v-card-subtitle>
			Edit, delete, or manage the state of a game.
		</v-card-subtitle>

		<v-card-text>

			<progress-bar :show="games.loading" />

			<!-- Display this after games have loaded (or an error occurred) -->
			<template v-if="!games.loading">

				<message type="error" :message="games.error" />

				<game-crud
					:id="crud.gameId"
					:confirmDestroy="crud.showDestroyDialog"
					@destroy="onDestroy"
					@cancel="onCancelCrud"
					@error="onErrorCrud"
				/>

				<!-- Clickable list of games -->
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

					<template v-slot:item.actions="{ item }">
						<v-icon small @click="destroy(item.id)">delete</v-icon>
						<v-icon small @click="viewGame(item.id)">view_list</v-icon>
					</template>

					<template v-slot:no-data>
						<v-row align="center" justify="start" v-if="!games.length">
							<v-col cols="12">
								<span class="warning">There aren't any games yet.</span>
							</v-col>
						</v-row>
					</template>

				</v-data-table>

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

	.v-card__actions .v-btn {
		font-size: 0.95rem;
		font-weight: bold;
	}

</style>

<script>

	import Message from './ui/Message';
	import Progress from './ui/Progress';

	import GameCrud from './crud/Game';
	import RequestMixin from '../mixins/Request.vue';

	export default {

		mounted() {

			this.loadGames();
		},

		data() {

			return {

				table: {

					search: '',

					headers: [
						{text: 'ID', value: 'id'},
						{text: 'Name', value: 'name'},
						{text: 'Definition', value: 'definition'},
						{text: 'Title', value: 'title'},
						{text: 'Author', value: 'author'},
						{text: 'Actions', value: 'actions'}
					],
				},

				games: {

					// Set to true whenever we're loading the data via the
					// API.
					loading: true,

					// If an error occurred during loading, that error
					// message will be set here.
					error: '',

					// This is the data that's returned from the API call.
					data: []
				},

				crud: {

					// If we're deleting or editing a game, this is the id
					// that gets passed into the CRUD component
					gameId: 0,

					// Toggles the game destruction confirmation dialog
					showDestroyDialog: false
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
			viewGame(id) {

				this.$emit('navigate', '/admin/games/' + id);
			},

			// Loads list of games via the API.
			loadGames() {

				this.games.loading = true;
				this.games.error = '';

				axios
					.get('/admin/api/games')

					.then(response => {

						this.games.data = response.data;
					})

					.catch(error => {
						this.games.error = this.getResponseError(error);
					})

					.finally(() => {
						this.games.loading = false;
					});
			},

			// Initiate a destroy operation pending confirmation
			destroy(id) {

				this.crud.gameId = id;
				this.crud.showDestroyDialog = true;
			},

			// Called when a game is successfully destroyed
			onDestroy(id) {

				// TODO: display confirmation message
				this.crud.showDestroyDialog = false;
				this.loadGames();
			},

			// Called when a user cancels a CRUD operation
			onCancelCrud(type, id) {

				switch (type) {

					case 'destroy':
						this.crud.showDestroyDialog = false;
						break;

					default:
						break;
				};
			},

			// Called when a CRUD operation results in an error
			onErrorCrud(type, id, message) {

				this.games.error = message;

				setTimeout(() => {
					this.games.error = '';
				}, 5000);
			}
		},

		components: {
			'message': Message,
			'progress-bar': Progress,
			'game-crud': GameCrud
		},

		mixins: [
			RequestMixin
		]
	};

</script>
