<template>

	<v-hover v-slot="{ hover }">

		<v-sheet
			:elevation="elevationComputed"
			:width="width"
			:height="height"
			:class="{ 'on-hover': hover }"
			shaped
			@click="$emit('click');"
		>

			<h3 class="title">{{ title }}</h3>
			<div class="description"><span v-if="description">{{ description }}</span></div>
			<v-icon style="padding-top: 20px;" x-large>{{ icon }}</v-icon>

		</v-sheet>

	</v-hover>

</template>


<style scoped>

.v-sheet {
	font-family: "Inconsolata", monospace;
	background-color: #303030; /* #2a2a2a */
	text-align: center;
	transition: opacity .2s ease-in-out;
	padding: 10px;
	-webkit-user-select: none; /* Safari */        
	-moz-user-select: none; /* Firefox */
	-ms-user-select: none; /* IE10+ and Edge */
	user-select: none; /* Standard */
}

.v-sheet .title {
	color: #ffffff !important;
	font-size: 1.5rem !important;
	font-weight: 700 !important;
	font-family: "Inconsolata", monospace;
}

.v-sheet .description {
	height: 3.2rem;
	line-height: 3.2rem;
	overflow: hidden;
	text-overflow: ellipsis;
	font-size: 0.9rem;
}

.v-sheet.on-hover {
	cursor: pointer;
}

.v-sheet:not(.on-hover) {
	opacity: 0.85;
}

</style>


<script>

	export default {

		computed: {

			elevationComputed() {

				return 'undefined' == typeof this.elevation ? 1 : this.elevation;
			}
		},

		props: {

			width: Number,
			height: Number,
			elevation: Number,
			description: String,

			title: {
				type: String,
				required: true
			},

			icon: {
				type: String,
				required: true
			}
		},

		data: function () {

			return {
				//
			};
		},

		methods: {

			// TOOD
		}
	};

</script>