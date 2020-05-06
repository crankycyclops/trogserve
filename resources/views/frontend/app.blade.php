<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name') }}</title>

		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="/css/vuetify{{ config('app.vuetifyVersion') }}.min.css">
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/app/app.css') }}">

		<script>

			// This is how I'm currently passing static data from Laravel to Vue.js
			window.playerNameMaxLen = {{ config('validation.newGame.playerNameMaxLen') }};
			window.playerNameMaxLenMsg = "<?= config('validation.newGame.playerNameMaxLenMsg') ?>";

		</script>

	</head>

	<body>

		<div id="app"></div>

		<script src="{{ mix('/js/app/manifest.js') }}"></script>
		<script src="{{ mix('/js/app/vendor.js') }}"></script>
		<script src="{{ mix('/js/app/app.js') }}"></script>

	</body>
</html>
