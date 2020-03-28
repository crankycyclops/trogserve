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

<form action="{{ route('admin.auth') }}" method="POST">

	<label for="username">Username:</label>
	<input type="text" name="username" id="username" />

	<label for="password">Password:</label>
	<input type="password" name="password" id="password" />

	@csrf

	<button type="submit">Submit</button>

</form>
