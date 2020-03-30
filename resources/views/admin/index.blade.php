<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name') }} - Admin</title>

		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/admin/app.css') }}">
		<link rel="stylesheet" type="text/css" href="/css/vuetify.min.css">

		<script>
			// This is how I'm currently passing data from PHP to Vue.js
			window.title = "{{ config('app.name') }}";
		</script>

	</head>

	<body>

		<div id="app"></div>
		<a href="{{ route('admin.logout') }}">Logout</a>

		<script src="{{ mix('/js/admin/manifest.js') }}"></script>
		<script src="{{ mix('/js/admin/vendor.js') }}"></script>
		<script src="{{ mix('/js/admin/app.js') }}"></script>

	</body>
</html>
