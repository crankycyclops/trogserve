<template>

	<v-card>

		<v-card-title>Players</v-card-title>

		<v-card-subtitle>Manage players in the game.</v-card-subtitle>

		<v-card-text>

			TODO

		</v-card-text>

		<v-card-actions>

			<v-btn
				text
				color="primary"
				@click="$emit('navigate', '/admin/games/' + $route.params.id)"
			>
				Back to Game
			</v-btn>

		</v-card-actions>

	</v-card>

</template>

<script>

	export default {

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
