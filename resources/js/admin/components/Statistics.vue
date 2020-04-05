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

			<v-row align="center" justify="start" v-if="statistics.loading">
				<v-col cols="12">
					<v-progress-linear :active="true" :indeterminate="true" />
				</v-col>
			</v-row>

			<div v-else>

				<v-row align="center" justify="start" v-if="statistics.loadingError">

					<v-col cols="12">
						<strong>Trogdord status:</strong>
						<span class="error">Offline</span>
					</v-col>

					<v-col cols="12">
						<span class="error">{{ statistics.loadingError }}</span>
					</v-col>

				</v-row>

				<template v-else>

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

			</div>

		</v-card-text>

		<v-card-actions>

			<v-btn text color="primary" @click="loadStatistics()">
				Refresh
			</v-btn>

		</v-card-actions>

	</v-card>

</template>

<script>

	export default {

		mounted: function () {

			this.loadStatistics();
		},

		computed: {
			// This is how I suck data in from Laravel
			phpVersion: function () {return window.phpVersion;},
			extVersion: function () {return window.extVersion;},
			lastLoginAt: function () {return window.lastLoginAt;},
			lastLoginIp: function () {return window.lastLoginIp;}
		},

		data: function () {
			return {

				// Server statistics that we load via an API call
				statistics: {

					// Set this to true whenever we load or reload this data
					loading: true,

					// If there was an error loading the statistics, this
					// will be an error message explaining what went wrong
					loadingError: null,

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

			loadStatistics: function () {

				this.statistics.loading = true;
				this.statistics.loadError = null;

				let self = this;

				axios
					.get('/admin/api/info')

					.then(response => {

						self.statistics.trogdordVersion =
							response.data.version.major + '.' +
							response.data.version.minor + '.' +
							response.data.version.patch;

						self.statistics.libtrogdorVersion =
							response.data.lib_version.major + '.' +
							response.data.lib_version.minor + '.' +
							response.data.lib_version.patch;

						self.statistics.players = response.data.players;
					})

					.catch(error => {

						if ('undefined' !== typeof(error.response)) {
							self.statistics.loadingError = error.response.data.error;
						} else {
							self.statistics.loadingError = error.message;
						}
					})

					.finally(() => {
						self.statistics.loading = false;
					});
			}
		}
	};

</script>
