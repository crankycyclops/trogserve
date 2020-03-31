<template>

	<v-app id="trogserve">

		<v-navigation-drawer
			v-model="mainDrawer.open"
			:permanent="false"
			:temporary="false"
			app
			overflow 
		>

			<v-list-item>

				<v-list-item-content>

					<v-list-item-title class="title">
						Main Menu
					</v-list-item-title>

					<v-list-item-subtitle>
						Welcome back, {{ username }}!
					</v-list-item-subtitle>

				</v-list-item-content>

			</v-list-item>

			<v-divider />

		</v-navigation-drawer>

		<v-app-bar :clipped-left="false" app>

			<v-app-bar-nav-icon
				@click.stop="mainDrawer.open = !mainDrawer.open"
			/>

			<v-toolbar-title>{{ title }}</v-toolbar-title>

		</v-app-bar>

		<v-content>

			<v-container fluid>

				<v-row align="center" justify="center">

					<v-col cols="12" md="11">
						<router-view></router-view>
					</v-col>

				</v-row>

			</v-container>

		</v-content>

		<v-footer app>
			<span class="px-4">&copy;{{ new Date().getFullYear() }} James Colannino.</span>
		</v-footer>

	</v-app>

</template>

<script>

	export default {

		computed: {
			// This is how I suck data in from Laravel
			title: function () {return window.title;},
			username: function () {return window.username}
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
		}
	};

</script>
