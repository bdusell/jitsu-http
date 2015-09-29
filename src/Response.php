<?php

namespace Jitsu;

/**
 * Utilities for building the current HTTP response about to be sent.
 */
class Response {

	const RESPONSE_BODY_STREAM = 'php://output';

	/**
	 * Set the response status line.
	 *
	 * Note that this is mutually exclusive with `code`.
	 *
	 * @param string $version
	 * @param int|string $code
	 * @param string $reason
	 */
	public static function setStatus($version, $code, $reason) {
		header("$version $code $reason");
	}

	/**
	 * Set the response code.
	 *
	 * Automatically sets an appropriate reason string.
	 *
	 * Note that this is mutually exclusive with `status`.
	 *
	 * @param int|string $code
	 */
	public static function setStatusCode($code) {
		return http_response_code($code);
	}

	/**
	 * Get the currently set response code.
	 */
	public static function statusCode() {
		return http_response_code();
	}

	/**
	 * Set a header in the response.
	 *
	 * Overwrites any previous header with the same name. Must be called
	 * before output is written, just like PHP `header`.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public static function addHeader($name, $value) {
		header("$name: $value");
	}

	/**
	 * Set a cookie in the response.
	 *
	 * @param string $name
	 * @param string $value
	 * @param int|null $expires
	 * @param string|null $path
	 * @param string|null $domain
	 */
	public static function addCookie(
		$name,
		$value,
		$expires = null,
		$path = null,
		$domain = null
	) {
		setcookie(
			$name,
			$value,
			$expires === null ? 0 : $expires,
			$path,
			$domain
		);
	}

	/**
	 * Indicate in the response to delete a cookie.
	 *
	 * @param string $name
	 * @param string|null $domain
	 * @param string|null $path
	 */
	public static function deleteCookie($name, $domain = null, $path = null) {
		setcookie($name, '', 1, $path, $domain);
	}

	/**
	 * Issue an HTTP redirect.
	 *
	 * @param string $url
	 * @param int|string $code
	 */
	public static function redirect($url, $code) {
		header('Location: ' . $url, true, $code);
	}

	/**
	 * Start buffering PHP output.
	 */
	public static function startOutputBuffering() {
		ob_start();
	}

	/**
	 * Flush the PHP output buffer.
	 */
	public static function flushOutputBuffer() {
		ob_end_flush();
	}

	/**
	 * Discard the contents of the PHP output buffer.
	 */
	public static function clearOutputBuffer() {
		ob_end_clean();
	}

	/**
	 * Determine whether the headers were sent.
	 *
	 * If the headers were already sent, they may no longer be modified.
	 *
	 * @return bool
	 */
	public static function sentHeaders() {
		return headers_sent();
	}

	/**
	 * Get a handle to a writable file stream to the response body.
	 *
	 * @return resource
	 */
	public static function bodyStream() {
		return fopen(self::RESPONSE_BODY_STREAM, 'w');
	}

	/**
	 * Shorthand for sending a PHP array as a JSON object in the response.
	 *
	 * @param array $obj
	 * @param bool $pretty Whether to pretty-print the JSON output.
	 */
	public static function json($obj, $pretty = false) {
		self::content_type('application/json');
		echo JSONUtil::encode($obj, $pretty);
	}

	/**
	 * Shorthand for sending a file with a given content type in the
	 * response.
	 *
	 * @param string $path
	 * @param string $content_type
	 */
	public static function file($path, $content_type) {
		self::content_type($content_type);
		readfile($path);
	}
}
