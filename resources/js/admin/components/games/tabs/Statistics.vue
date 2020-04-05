<template>

	<v-card flat>

		<v-card-title>Statistics</v-card-title>

		<v-card-subtitle>
			Various per-game statistics.
		</v-card-subtitle>

		<v-card-text>

			<v-row align="center" justify="start">

				<v-col cols="12" md="6">
					<strong>Created:</strong>
					{{ created }}
				</v-col>

				<v-col cols="12" md="6">
					<strong>Total players:</strong>
					{{ players }}
				</v-col>

			</v-row>

		</v-card-text>

		<v-card-actions>

				<v-btn text color="primary" @click="$emit('refresh')">
					Refresh
				</v-btn>

		</v-card-actions>

	</v-card>

</template>

<script>

	export default {

		props: {

			created: {
				type: String
			},

			players: {
				type: Number
			}
		},

		data: function () {

			return {
				//
			};
		},

		methods: {
			//
		}
	};

</script>
