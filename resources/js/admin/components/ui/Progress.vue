<template>

	<v-row align="center" justify="start" v-if="show">
		<v-col cols="12">
			<v-progress-linear :active="true" :indeterminate="true" />
		</v-col>
	</v-row>

</template>


<script>

	export default {

		props: {
			show: Boolean
		}
	};

</script>