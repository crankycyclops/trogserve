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

mix.js('resources/js/app.js', 'public/js')
	.sass('resources/sass/app.scss', 'public/css')
	.copy('node_modules/vuetify/dist/vuetify.min.css', 'public/css/vuetify.min.css')
	.copy('node_modules/vuetify/dist/vuetify.css.map', 'public/css/vuetify.css.map')
	.extract()
	.sourceMaps()
	.mergeManifest();

// Version files in production for cache-busting purposes
if (mix.inProduction()) {
	mix.version();
}
