/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

//const files = require.context('./', true, /\.vue$/i)
//files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vuetify from 'vuetify';
import VueRouter from 'vue-router';
import Vuex from 'vuex';

import App from './App.vue';

import Games from './components/Games.vue';
import Game from './components/Game.vue';

Vue.use(Vuetify);
Vue.use(VueRouter);
Vue.use(Vuex);

const app = new Vue({

	el: '#app',
	render: h => h(App),

	vuetify: new Vuetify({

		theme: {
			dark: true,
		},

		// https://jossef.github.io/material-design-icons-iconfont/
		icons: {
			iconfont: 'md'
		}
	}),

	router: new VueRouter({

		mode: 'history',

		routes: [

			{
				// List of available games
				path: '/games',
				name: 'games',
				component: Games
			},

			{
				// Console for a specific game
				path: '/games/:id(\\d+)',
				name: 'game',
				component: Game
			}
		]
	}),

	store: new Vuex.Store({

		state: {

			// If set, we should display this error message at the top of the
			// frontend application
			error: null,

			// The title we should display at the top of the application
			title: ''
		},

		mutations: {

			// Sets the error to display at the top of the application
			setError: function (state, payload) {
				state.error = payload;
			},

			// Sets the title at the top of the application
			setTitle: function (state, payload) {
				state.title = payload;
			}
		},

		getters: {},
		actions: {}
	}),

	components: {
		App
	}
});
