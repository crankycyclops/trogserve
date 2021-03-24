<template>

	<v-card>

		<v-card-title>{{ crud.showEditForm ? 'Edit Details' : 'Games' }}</v-card-title>

		<v-card-subtitle>
			{{ crud.showEditForm ? "Change basic details such as the game's title and author." : "Edit, delete, or manage the state of a game." }}
		</v-card-subtitle>

		<v-card-text>

			<progress-bar :show="games.loading" />

			<template v-if="!games.loading">

				<message :type="games.messageType" :message="games.message" />

				<!-- Allows editing and deleting games in the table -->
				<game-crud ref="crud"
					:id="crud.gameId"
					:data="crud.game"
					:definitions="crud.definitions"
					:confirmDestroy="crud.showDestroyDialog"
					:showForm="crud.showEditForm"
					@dump="onDump"
					@destroy="onDestroy"
					@edit="onEdit"
					@cancel="onCancelCrud"
					@error="onErrorCrud"
				/>

				<!-- List of games -->
				<template v-if="!crud.showEditForm">

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

							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon small v-bind="attrs" v-on="on" @click="edit(item.id)">edit</v-icon>
								</template>
								<span>Edit</span>
							</v-tooltip>

							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon small v-bind="attrs" v-on="on" @click="dump(item.id)">file_download</v-icon>
								</template>
								<span>Dump</span>
							</v-tooltip>

							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon small v-bind="attrs" v-on="on" @click="destroy(item.id)">delete</v-icon>
								</template>
								<span>Delete</span>
							</v-tooltip>

							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon small v-bind="attrs" v-on="on" @click="viewGame(item.id)">view_list</v-icon>
								</template>
								<span>Manage</span>
							</v-tooltip>

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

			</template>

		</v-card-text>

		<v-card-actions v-if="!games.loading">

				<template v-if="crud.showEditForm">

					<v-btn text color="primary" @click="crud.showEditForm = false;">
						Cancel
					</v-btn>

					<v-btn text color="primary" @click="$refs.crud.submit();">
						Finish
					</v-btn>

				</template>

				<template v-else>

					<v-btn text color="primary" @click="loadGames()">
						Refresh
					</v-btn>

					<v-btn
						text
						color="primary"
						:disabled="games.loadFailed ? true : false"
						@click="$emit('navigate', '/admin/games/new');"
					>
						Create Game
					</v-btn>

				</template>

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

					// Set this to true if loading the list of games failed.
					loadFailed: false,

					// Setting this to a string displays an error or
					// confirmation message after some operation, or 
					// after the page has loaded if there wre problems.
					message: '',

					// One of 'error' or 'success'
					messageType: 'error',

					// This is the data that's returned from the API call.
					data: [],

					// Maps game ids to keys in the data property above.
					// This is necessary because I can't make data an
					// Object; the data table component requires an array.
					keys: {}
				},

				crud: {

					// If we're deleting or editing a game, this is the id
					// that gets passed into the CRUD component
					gameId: 0,

					// If we're editing a game, this object will contain its
					// data
					game: {},

					// If we're editing a game, this will be an array with a
					// single value: the definition file used to create it.
					// This will populate the disabled drop-down.
					definitions: [],

					// Toggles the game destruction confirmation dialog
					showDestroyDialog: false,

					// Toggles the edit form for a game
					showEditForm: false
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

				this.games.data = [];
				this.games.loading = true;
				this.games.message = '';

				axios
					.get('/admin/api/games')

					.then(response => {

						this.games.data = response.data;

						response.data.forEach((game, i) => {
							this.games.keys[game.id] = i;
						});

						this.games.loadFailed = false;
					})

					.catch(error => {
						this.games.message = this.getResponseError(error);
						this.games.messageType = 'error';
						this.games.loadFailed = true;
					})

					.finally(() => {
						this.games.loading = false;
					});
			},

			// Dump the game
			dump(id) {

				this.crud.gameId = id;
				this.$refs.crud.dump();
			},

			// Initiate an edit operation
			edit(id) {

				let game = this.games.data[this.games.keys[id]];

				this.crud.gameId = id;
				this.crud.definitions = [game.definition];
				this.crud.game = game;
				this.crud.showEditForm = true;
			},

			// Called when the game has been successfully dumped
			onDump(id) {

				this.games.message = 'Game dumped';
				this.games.messageType = 'success';

				setTimeout(() => {
					this.games.message = '';
				}, 5000);
			},

			// Called when a game is successfully edited
			onEdit(id, changed) {

				// TODO: display confirmation message
				this.crud.showEditForm = false;

				if (0 === Object.keys(changed).length) {
					this.loadGames();
				}
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

				// If we're editing, we'll let the component display the error
				// above the form
				if ('edit' != type) {

					this.games.message = message;
					this.games.messageType = 'error';

					setTimeout(() => {
						this.games.message = '';
					}, 5000);
				}
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
