<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name') }} - Admin</title>

		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="/css/vuetify{{ config('app.vuetifyVersion') }}.min.css">
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/admin/app.css') }}">

		<script>

			// This is how I'm currently passing static data from Laravel to Vue.js
			window.phpVersion = "{{ phpversion() }}";
			window.extVersion = "{{ phpversion('trogdord') }}";

			window.title = "{{ config('app.name') }} Admin";
			window.username = "{{ Auth::user()->username }}";

			window.lastLoginAt = <?= Auth::user() && Auth::user()->last_login_at ?
				'"' . Auth::user()->last_login_at . '"' : 'null' ?>;
			window.lastLoginIp = <?= Auth::user() && Auth::user()->last_login_ip ?
				'"' . Auth::user()->last_login_ip . '"' : 'null' ?>;

			// Used for validation
			window.nameMaxLen = {{ config('validation.newGame.nameMaxLen') }};
			window.titleMaxLen = {{ config('validation.newGame.titleMaxLen') }};
			window.authorMaxLen = {{ config('validation.newGame.authorMaxLen') }};
			window.synopsisMaxLen = {{ config('validation.newGame.synopsisMaxLen') }};
			window.playerNameMaxLen = {{ config('validation.newGame.playerNameMaxLen') }};

			window.nameMaxLenMsg = "<?= config('validation.newGame.nameMaxLenMsg') ?>";
			window.titleMaxLenMsg = "<?= config('validation.newGame.titleMaxLenMsg') ?>";
			window.authorMaxLenMsg = "<?= config('validation.newGame.authorMaxLenMsg') ?>";
			window.synopsisMaxLenMsg = "<?= config('validation.newGame.synopsisMaxLenMsg') ?>";
			window.playerNameMaxLenMsg = "<?= config('validation.newGame.playerNameMaxLenMsg') ?>";

		</script>

	</head>

	<body>

		<div id="app"></div>

		<script src="{{ mix('/js/admin/manifest.js') }}"></script>
		<script src="{{ mix('/js/admin/vendor.js') }}"></script>
		<script src="{{ mix('/js/admin/app.js') }}"></script>

	</body>
</html>
