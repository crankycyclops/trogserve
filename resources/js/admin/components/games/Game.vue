<template>

	<v-card>

		<v-card-title>TODO: game title or name</v-card-title>

		<v-card-subtitle>
			TODO: game by line (if available)
		</v-card-subtitle>

		<v-card-text>
			TODO
		</v-card-text>

		<v-card-actions>

			<v-btn
				text
				color="primary"
				@click="$router.push('/admin/games')"
			>
				Browse Games
			</v-btn>

			<v-btn
				text
				color="error"
				@click="destroy()"
			>
				Destroy
			</v-btn>

		</v-card-actions>

	</v-card>

</template>

<script>

	export default {

		mounted: function () {

			//
		},

		data: function () {

			return {
				//
			};
		},

		methods: {

			// Destroy the game
			destroy: function () {

				// TODO: replace with dialog and actually destroy
				alert('Are you sure you want to destroy this game? This action is permanent and cannot be undone.');
				this.$router.push('/admin/games');
			}
		}
	};

</script>
