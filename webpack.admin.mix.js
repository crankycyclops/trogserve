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

mix.js('resources/js/admin/app.js', 'public/js/admin')
	.sass('resources/sass/admin/app.scss', 'public/css/admin')
	.extract()
	.sourceMaps()
	.mergeManifest()
	.vue({ version: 2 });

// Version files in production for cache-busting purposes
if (mix.inProduction()) {
	mix.version();
}
