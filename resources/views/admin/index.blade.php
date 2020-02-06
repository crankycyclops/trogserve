<!doctype html>

<html>

	<head>
		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/admin/app.css') }}" >
	</head>

	<body>

		<div id="app"></div>

		<script src="{{ mix('/js/admin/manifest.js') }}"></script>
		<script src="{{ mix('/js/admin/vendor.js') }}"></script>
		<script src="{{ mix('/js/admin/app.js') }}"></script>

	</body>
</html>
