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

mix.js('resources/js/adminauth/app.js', 'public/js/adminauth')
	.sass('resources/sass/adminauth/app.scss', 'public/css/adminauth')
	.extract()
	.sourceMaps()
	.mergeManifest();

// Version files in production for cache-busting purposes
if (mix.inProduction()) {
	mix.version();
}
