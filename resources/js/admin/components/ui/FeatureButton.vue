<template>

	<v-hover v-slot="{ hover }">

		<v-sheet
			ref="sheet"
			:elevation="elevationComputed"
			:width="width"
			:height="height"
			:class="{ 'on-hover': hover, 'clicking': clicking, 'disabled': disabled }"
			shaped
			@click="if (!disabled) $emit('click');"
			@mousedown="mouseDown()"
			@mouseleave="mouseLeave()"
		>

			<h3 class="title">{{ title }}</h3>
			<div class="description" v-if="!hideDescription"><span v-if="description">{{ description }}</span></div>
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

.v-sheet.disabled {
	background-color: #2a2a2a;
	opacity: 0.7;
}

.v-sheet:not(.disabled).clicking {
	background-color: #404040;
}

.v-sheet:not(.disabled).on-hover {
	cursor: pointer;
}

.v-sheet:not(.disabled):not(.on-hover) {
	opacity: 0.85;
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

.v-sheet.disabled .theme--dark.v-icon, .v-sheet.disabled .title {
	color: #bcbcbc !important;
}

</style>


<script>

	export default {

		mounted() {

			this.elevationComputed = 'undefined' == typeof this.elevation ? 1 : this.elevation;
		},

		props: {

			width: Number,
			height: Number,
			elevation: Number,
			description: String,
			hideDescription: Boolean,
			disabled: Boolean,

			title: {
				type: String,
				required: true
			},

			icon: {
				type: String,
				required: true
			}
		},

		data() {

			return {

				// True when the sheet is being clicked
				clicking: false,

				// The elevation will change based on whether or not
				// the sheet is being clicked
				elevationComputed: this.elevation
			};
		},

		methods: {

			mouseDown() {

				this.clicking = true;
				this.elevationComputed = 0;
			},

			mouseLeave() {

				this.clicking = false;
				this.elevationComputed = 'undefined' == typeof this.elevation ? 1 : this.elevation;
			}
		}
	};

</script>