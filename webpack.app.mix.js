const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app/app.js', 'public/js/app')
	.sass('resources/sass/app/app.scss', 'public/css/app')

	// I'm going to unbundle this so it'll remain cached when my own code changes
	.copy('node_modules/vuetify/dist/vuetify.min.css', 'public/css/vuetify.min.css')
	.copy('node_modules/vuetify/dist/vuetify.css.map', 'public/css/vuetify.css.map')

	.extract()
	.sourceMaps()
	.mergeManifest()
	.vue({ version: 2 });

// Version files in production for cache-busting purposes
if (mix.inProduction()) {
	mix.version();
}
