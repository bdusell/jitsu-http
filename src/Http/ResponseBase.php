<?php

namespace Jitsu\Http;

abstract class ResponseBase implements ResponseInterface {

	/**
	 * Set the status code of the response.
	 *
	 * @param int|string $code HTTP status code.
	 * @param string $reason An optional reason string. If not given, a
	 *                       default is used.
	 */
	public function setStatusCode($code, $reason = '') {
		$this->setStatus('HTTP/1.1', $code, $reason);
	}

	/**
	 * Get the current status code set on this response.
	 */
	abstract public function statusCode();

	/**
	 * Set the content type of the response.
	 *
	 * @param string $type
	 */
	public function setContentType($type) {
		$this->addHeader('Content-Type', $type);
	}

	/**
	 * Add a cookie to the response.
	 *
	 * @param string $name
	 * @param string $value
	 * @param (string|bool)[] $attrs An array of attributes to assign to
	 *                               the cookie. If an attribute value is
	 *                               a boolean, the value determines
	 *                               whether to include the attribute name
	 *                               in the cookie (e.g. for `Secure` or
	 *                               `HttpOnly`).
	 */
	public function addCookie($name, $value, $attrs) {
		$this->addHeader('Set-Cookie', self::formatCookie($name, $value, $attrs));
	}

	private static function formatCookie($name, $value, $attrs) {
		// TODO
	}

	/**
	 * Issue a redirect to another URL.
	 *
	 * Note that this does NOT exit the current process. Keep in mind that
	 * relying on the client to respect this header and close the
	 * connection for you is potentially a huge security hole.
	 *
	 * The response code is also set here. The response code should be a
	 * 3XX code.
	 */
	public function redirect($url, $code, $reason = '') {
		$this->setStatusCode($code, $reason);
		$this->addHeader('Location', $url); // TODO encode URL
	}

	/**
	 * Start buffering PHP output.
	 */
	public function startOutputBuffering() {
		ob_start();
	}

	/**
	 * Discard the contents of the PHP output buffer.
	 */
	public function clearOutputBuffer() {
		ob_end_clean();
	}
}
