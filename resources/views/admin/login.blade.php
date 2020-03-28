<!doctype html>

<html>

	<head>
		<!-- TODO: Generate critical CSS (see: https://laravel-mix.com/extensions/criticalcss) -->
		<link rel="stylesheet" type="text/css" href="{{ mix('/css/adminauth/app.css') }}">
	</head>

	<body>

		<main>

			<article class="login-panel">

				<h2>Trogserve Admin</h2>

				@if (Session::has('error'))
					<div class="alert alert-error">
						{{ Session::get('error') }}
					</div>
				@endif

				@if (Session::has('notification'))
					<div class="alert alert-notification">
						{{ Session::get('notification') }}
					</div>
				@endif

				<div id="login-prompt">

					<form action="{{ route('admin.auth') }}" method="POST">

						@csrf

						<div class="field">
							<input type="text" name="username" id="username" placeholder="Username" />
						</div>

						<div class="field">
							<input type="password" name="password" id="password" placeholder="Password" />
						</div>

						<div class="field">
							<button type="submit">Login</button>
						</div>

					</form>

				</div>

			</article>

			<aside class="side-matter">
				TODO: side matter
			</aside>

		</main>

		<script src="{{ mix('/js/adminauth/manifest.js') }}"></script>
		<script src="{{ mix('/js/adminauth/vendor.js') }}"></script>
		<script src="{{ mix('/js/adminauth/app.js') }}"></script>

	</body>

</html>
