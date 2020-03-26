<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	// If we're in production mode and a network error occurs, this is the
	// generic message we should return to the user.
	protected const GENERIC_500_MESSAGE = 'An error occured. Please try your query again in a few minutes.';

	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param  \Throwable  $exception
	 * @return void
	 *
	 * @throws \Throwable
	 */
	public function report(Throwable $exception) {

		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Throwable
	 */
	public function render($request, Throwable $exception) {

		if ($exception instanceof \Trogdord\NetworkException) {

			return response()->json([
				'error' => 'production' == config('app.env') ?
					self::GENERIC_500_MESSAGE : $exception->getMessage()
			], 500);
		}

		else if ($exception instanceof \Trogdord\RequestException) {

			$code = 404 == $exception->getCode() ? 404 : 500;

			return response()->json([
				'error' => $exception->getMessage(),
				'code'  => $exception->getCode()
			], $code);
		}

		else {
			return parent::render($request, $exception);
		}
	}
}
