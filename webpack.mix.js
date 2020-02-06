// TODO: Add critical CSS separation (see: https://laravel-mix.com/extensions/criticalcss)
// For an explanation of what I'm doing here, see:
// http://www.compulsivecoders.com/tech/how-to-build-multiple-vendors-using-laravel-mix/
// Requires: npm install laravel-mix-merge-manifest --save
// Another possible approach I can try if this ever breaks can be found here:
// https://stackoverflow.com/questions/45046696/laravel-mix-multiple-entry-points-generates-one-manifest-js/45134898#45134898
// All of these solutions were found on this Github issue:
// https://github.com/JeffreyWay/laravel-mix/issues/488
if (['admin', 'app'].includes(process.env.npm_config_section)) {
	require(`${__dirname}/webpack.${process.env.npm_config_section}.mix.js`);
}

else {

	console.log(
		'\x1b[41m%s\x1b[0m',
		'Provide correct --section argument to build command: admin, api'
	);

	throw new Error('Provide correct --section argument to build command!')
}
