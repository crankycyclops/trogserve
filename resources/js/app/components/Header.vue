<template>

	<header class="header">

		<a href="/" class="logo">{{ title }}</a>

		<input class="menu-btn" type="checkbox" id="menu-btn" />
		<label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>

		<ul class="menu">
			<li><a href="/games">Play a Game</a></li>
			<li><a href="https://github.com/crankycyclops/trogserve">View Code on GitHub</a></li>
		</ul>

	</header>

</template>

<style lang="scss" scoped>

	@import '../../../sass/_variables.scss';
	@import '../../../sass/pages/header.scss';

	// I have to adjust the imported header style a little to work with the
	// Vuetify layout
	.header {
	padding: 20px 20px 0 20px;
		width: 100%;
	}

	.header ul {
		text-shadow: 0 0 2px #989898;
	}

	// I also have to adjust the max-widths of the page title because Vue is
	// laid out a little different
	.header .logo {
		max-width: 77.5%;
	}

	@media (min-width: 781px) {

		.header .logo {
			max-width: 49%;
		}
	}

</style>

<script>

	export default {

		props: {

			// Page title
			title: {
				type: String
			},
		},

		data: function () {

			return {
				// TODO
			};
		},

		methods: {

		}
	};

</script>
