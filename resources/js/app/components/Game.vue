<template>

	<v-card>

		<v-card-title>{{ title }}</v-card-title>

		<v-card-subtitle>
			TODO
		</v-card-subtitle>

		<v-card-text>
			TODO
		</v-card-text>

	</v-card>

</template>

<script>

	export default {

		mounted: function () {

			this.$store.commit('setTitle', 'Game Title');
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
