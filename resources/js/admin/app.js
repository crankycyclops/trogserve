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
import Vue from 'vue';
import Vuetify from 'vuetify';
import VueRouter from 'vue-router';
import Vuex from 'vuex';

import App from './App.vue';

import Account from './components/Account.vue'
import Statistics from './components/Statistics.vue';
import State from './components/State.vue';
import Games from './components/Games.vue';
import NewGame from './components/games/New.vue';
import DisplayGame from './components/games/Game.vue';
import StateDump from './components/state/Dump.vue';
import StateRestore from './components/state/Restore.vue';
import StateGames from './components/state/Games.vue';

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
				path: '/admin/account',
				name: 'account',
				component: Account
			},

			{
				path: '/admin',
				name: 'statistics',
				component: Statistics
			},

			{
				path: '/admin/state',
				name: 'state',
				component: State
			},

			{
				path: '/admin/state/dump',
				name: 'state_dump',
				component: StateDump
			},

			{
				path: '/admin/state/restore',
				name: 'state_restore',
				component: StateRestore
			},

			{
				path: '/admin/state/games',
				name: 'state_games',
				component: StateGames
			},

			{
				path: '/admin/games',
				name: 'games',
				component: Games
			},

			{
				path: '/admin/games/new',
				name: 'newGame',
				component: NewGame
			},

			{
				path: '/admin/games/:id(\\d+)',
				name: 'displayGame',
				component: DisplayGame
			}
		]
	}),

	store: new Vuex.Store({

		state: {

			// If set, we should display this error message at the top of the
			// admin panel.
			error: null
		},

		mutations: {

			// Sets the error to display at the top of the admin panel.
			setError(state, payload) {
				state.error = payload;
			}
		},

		getters: {},
		actions: {}
	}),

	components: {
		App
	}
});
