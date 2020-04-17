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

mix.js('resources/js/pages/app.js', 'public/js/pages')
	.sass('resources/sass/pages/app.scss', 'public/css/pages')
	.extract()
	.sourceMaps()
	.mergeManifest();

// Version files in production for cache-busting purposes
if (mix.inProduction()) {
	mix.version();
}
