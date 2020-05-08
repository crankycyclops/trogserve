<template>

	<v-app id="trogserve">

		<v-app-bar :clipped-left="false" app>

			<v-toolbar-title>{{ $store.state.title }}</v-toolbar-title>

		</v-app-bar>

		<v-content>

			<v-container fluid>

				<v-row align="center" justify="center" :style="!$store.state.error ? 'display: none;' : ''">
					<v-col cols="12" md="11">
						<span class="error">{{ $store.state.error }}</span>
					</v-col>
				</v-row>

				<v-row align="center" justify="center">
					<v-col cols="12" md="11">
						<router-view @navigate="navigate" />
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

		data: function () {

			return {
				// TODO
			};
		},

		methods: {

			// Returns true if the specified route points to the current page
			// and false if not.
			isLinkToSelf: function (route) {

				return route == this.$route.path ? true : false;
			},

			// Navigate to the specified route (if we aren't already there.)
			// If a second argument is passed in and that value is true, the
			// error message at the top of the page will not be reset.
			navigate: function (route, preserveError) {

				if (!this.isLinkToSelf(route)) {

					this.$router.push(route);

					if (!preserveError) {
						this.$store.commit('setError', null);
					}
				}
			}
		}
	};

</script>
