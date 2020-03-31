<template>

	<v-app id="trogserve">

		<v-navigation-drawer
			v-model="navDrawer.open"
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

			<v-list nav>

				<v-list-item
					v-for="page in pages"
					:key="page.name"
					:link="!isLinkToSelf(page.route)"
					:disabled="isLinkToSelf(page.route)"
					@click="navigate(page.route);"
				>

					<v-list-item-icon>
						<v-icon dark>{{ page.icon }}</v-icon>
					</v-list-item-icon>

					<v-list-item-content>
						<v-list-item-title>{{ page.name }}</v-list-item-title>
					</v-list-item-content>

				</v-list-item>

				<v-list-item link href="/admin/logout">

					<v-list-item-icon>
						<v-icon dark>exit_to_app</v-icon>
					</v-list-item-icon>

					<v-list-item-content>
						<v-list-item-title>Logout</v-list-item-title>
					</v-list-item-content>

				</v-list-item>

			</v-list>

		</v-navigation-drawer>

		<v-app-bar :clipped-left="false" app>

			<v-app-bar-nav-icon
				@click.stop="navDrawer.open = !navDrawer.open"
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

				// Navigation drawer links for each route
				pages: [

					{
						name: "Statistics",
						route: "/admin",
						icon: "bar_chart"
					},

					{
						name: "Games",
						route: "/admin/games",
						icon: "videogame_asset"
					},
				],

				navDrawer: {

					// Open by default on Desktop and closed by default on mobile
					open: null
				}
			};
		},

		methods: {

			// Returns true if the specified route points to the current page
			// and false if not.
			isLinkToSelf: function (route) {

				return route == this.$route.path ? true : false;
			},

			// Navigate to the specified route (if we aren't already there)
			navigate: function (route) {

				if (!this.isLinkToSelf(route)) {
					this.$router.push(route);
				}
			}
		}
	};

</script>
