<template>

	<v-app id="trogserve">

		<v-navigation-drawer
			v-model="mainDrawer.open"
			:permanent="false"
			:temporary="false"
			app
			overflow 
		/>

		<v-app-bar :clipped-left="false" app>

			<v-app-bar-nav-icon
				@click.stop="mainDrawer.open = !mainDrawer.open"
			/>

			<v-toolbar-title>{{ title }}</v-toolbar-title>

		</v-app-bar>

		<v-content>

			<v-container fluid>

				<v-row align="center" justify="center">

					<v-col cols="10">

						<v-card>

							<v-card-text>
								TODO: this is placeholder text.
							</v-card-text>

						</v-card>

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
			lastLoginAt: function () {return window.lastLoginAt;},
			lastLoginIp: function () {return window.lastLoginIp;}
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
		},

		components: {
			//
		}
	};

</script>
