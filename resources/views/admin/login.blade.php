<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name') }} - Login</title>

		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/adminauth/app.css') }}">

	</head>

	<body>

		<main>

			<article class="login-panel">

				<h1>Trogserve Admin</h1>

				@if (Session::has('error'))
					<div class="alert error">
						{{ Session::get('error') }}
					</div>
				@endif

				@if (Session::has('notification'))
					<div class="alert success">
						{{ Session::get('notification') }}
					</div>
				@endif

				<div id="login-prompt">

					<form action="{{ route('admin.auth') }}" method="POST">

						<div class="login-fields">

							<div class="field">
								<input type="text" name="username" id="username" placeholder="Username" />
							</div>

							<div class="field">
								<input type="password" name="password" id="password" placeholder="Password" />
							</div>

							@csrf

						</div>

						<div class="field">
							<button type="submit">Login</button>
						</div>

					</form>

				</div>

			</article>

			<aside class="side-matter">
				<!-- TODO: side matter -->
			</aside>

		</main>

		<script src="{{ mix('/js/adminauth/manifest.js') }}"></script>
		<script src="{{ mix('/js/adminauth/vendor.js') }}"></script>
		<script src="{{ mix('/js/adminauth/app.js') }}"></script>

	</body>

</html>
