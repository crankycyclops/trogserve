<template>

	<v-card>

		<v-card-title>Statistics</v-card-title>

		<v-card-subtitle>
			Useful information about the server and the underlying instance of trogdord.
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

	</v-card>

</template>

<script>

	export default {

		computed: {
			// This is how I suck data in from Laravel
			lastLoginAt: function () {return window.lastLoginAt;},
			lastLoginIp: function () {return window.lastLoginIp;}
		},

		data: function () {
			return {

				mainDrawer: {

					// Open by default on Desktop and closed by default on mobile
					open: null
				}
			};
		},

		methods: {
			//
		},

		components: {
			//
		}
	};

</script>
