<template>

	<v-card>

		<v-card-title>Login Details</v-card-title>

		<v-card-subtitle>
			Information about when and where you last logged in.
		</v-card-subtitle>

		<v-card-text>

			<v-row align="center" justify="start">

				<v-col cols="12" md="6">
					<strong>Last logged in at:</strong>
					{{ lastLoginAt }}
				</v-col>

				<v-col cols="12" md="6">
					<strong>Last logged in from:</strong>
					{{ lastLoginIp }}
				</v-col>

			</v-row>

		</v-card-text>

		<v-card-title>Statistics</v-card-title>

		<v-card-subtitle>
			Useful information about the server and the underlying instance of trogdord.
		</v-card-subtitle>

		<v-card-text>

			<progress-bar :show="statistics.loading" />

			<template v-if="!statistics.loading">

				<v-row align="center" justify="start" v-if="statistics.loadingError">

					<v-col cols="12">
						<strong>Trogdord status:</strong>
						<span class="error">Offline</span>
					</v-col>

				</v-row>

				<message type="error" :message="statistics.loadingError" />

				<template v-if="!statistics.loadingError">

					<v-row align="center" justify="start">

						<v-col cols="12">
							<strong>Trogdord status:</strong>
							<span class="success">Online</span>
						</v-col>

					</v-row>

					<v-row align="center" justify="start">

						<v-col cols="12" md="6">
							<strong>Total games:</strong>
							{{ statistics.games }}
						</v-col>

						<v-col cols="12" md="6">
							<strong>Total players:</strong>
							{{ statistics.players }}
						</v-col>

						<v-col cols="12" md="6">
							<strong>PHP version:</strong>
							{{ phpVersion }}
						</v-col>

						<v-col cols="12" md="6">
							<strong>Extension version:</strong>
							{{ extVersion }}
						</v-col>

						<v-col cols="12" md="6">
							<strong>Trogdord version:</strong>
							{{ statistics.trogdordVersion }}
						</v-col>

						<v-col cols="12" md="6">
							<strong>Libtrogdor version:</strong>
							{{ statistics.libtrogdorVersion }}
						</v-col>

					</v-row>

				</template>

			</template>

		</v-card-text>

		<v-card-actions>

			<v-btn text color="primary" @click="loadStatistics()">
				Refresh
			</v-btn>

		</v-card-actions>

	</v-card>

</template>

<script>

	import Progress from './ui/Progress';
	import Message from './ui/Message';

	import RequestMixin from '../mixins/Request.vue';

	export default {

		mounted() {

			this.loadStatistics();
		},

		computed: {
			// This is how I suck data in from Laravel
			phpVersion() {return window.phpVersion;},
			extVersion() {return window.extVersion;},
			lastLoginAt() {return window.lastLoginAt;},
			lastLoginIp() {return window.lastLoginIp;}
		},

		data() {

			return {

				// Server statistics that we load via an API call
				statistics: {

					// Set this to true whenever we load or reload this data
					loading: true,

					// If there was an error loading the statistics, this
					// will be an error message explaining what went wrong
					loadingError: '',

					// Trogdord's version
					trogdordVersion: null,

					// The version of libtrogdor that the current instance
					// of trogdord was compiled against
					libtrogdorVersion: null,

					// The total number of games running in trogdord
					games: 'TODO',

					// The total number of players in all games
					players: null
				}
			};
		},

		methods: {

			loadStatistics() {

				this.statistics.loading = true;
				this.statistics.loadingError = '';

				axios
					.get('/admin/api/info')

					.then(response => {

						this.statistics.trogdordVersion =
							response.data.version.major + '.' +
							response.data.version.minor + '.' +
							response.data.version.patch;

						this.statistics.libtrogdorVersion =
							response.data.lib_version.major + '.' +
							response.data.lib_version.minor + '.' +
							response.data.lib_version.patch;

						this.statistics.players = response.data.players;
					})

					.catch(error => {
						this.statistics.loadingError = this.getResponseError(error);
					})

					.finally(() => {
						this.statistics.loading = false;
					});
			}
		},

		components: {
			'message': Message,
			'progress-bar': Progress
		},

		mixins: [
			RequestMixin
		]
	};

</script>
