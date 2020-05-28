<template>

	<v-app id="trogserve">

		<AppHeader :title="$store.state.title" />

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

<style lang="scss" scoped>

	// I have to use !important because Vuetify made this style inline and this
	// is the only way I can override it from my stylesheet. Grr.
	#trogserve main {
		padding: 0 !important;
	}

	// Make sure the main content is always displayed below the header
	@media screen and (max-width: 374px) {

		main {
			margin-top: 66px;
		}

		body::after {
			// 52px for a flush border
			top: 66px;
		}
	}

	@media screen and (min-width: 375px) {

		main {
			margin-top: 74px;
		}

		body::after {
			// 60px for a flush border
			top: 74px;
		}
	}

</style>

<script>

	import AppHeader from './components/Header.vue';

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
		},

		components: {
			AppHeader
		}
	};

</script>
