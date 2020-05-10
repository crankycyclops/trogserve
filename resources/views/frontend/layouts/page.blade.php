<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		@if (View::hasSection('title'))
			<title>{{ config('app.name') }} - @yield('title')</title>
		@else
			<title>{{ config('app.name') }}</title>
		@endif

		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/pages/app.css') }}">

	</head>

	<body>

		<main>

			<article class="content">
				@yield('content')
			</article>

			@if (View::hasSection('sidematter'))
				<aside class="side-matter">
					@yield('sidematter')
				</aside>
			@endif

		</main>

		<footer><span class="text">©2020 James Colannino.</span></footer>

		<script src="{{ mix('/js/pages/manifest.js') }}"></script>
		<script src="{{ mix('/js/pages/vendor.js') }}"></script>
		<script src="{{ mix('/js/pages/app.js') }}"></script>

	</body>

</html>
