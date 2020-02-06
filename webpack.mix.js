// For an explanation of what I'm doing here, see:
// http://www.compulsivecoders.com/tech/how-to-build-multiple-vendors-using-laravel-mix/
// Requires: npm install laravel-mix-merge-manifest --save
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
