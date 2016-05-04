<?php

namespace Jitsu\Http;

/**
 * An abstract interface to an HTTP response.
 */
interface ResponseInterface {

	/**
	 * Set the HTTP status line of the response.
	 *
	 * @param string $version An HTTP version string. The modern version
	 *                        string is `HTTP/1.1`.
	 * @param int|string $code An HTTP status code.
	 * @param string $reason A reason string describing the status code.
	 */
	public function setStatus($version, $code, $reason);

	/**
	 * Send an HTTP header in the response.
	 *
	 * Does not override previously sent headers with the same name.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addHeader($name, $value);

	/**
	 * Flush the PHP output buffer to the body of the response.
	 */
	public function flushOutputBuffer();

	/**
	 * Determine whether the headers were sent.
	 *
	 * If the headers were already sent, they may no longer be modified.
	 *
	 * @return bool
	 */
	public function sentHeaders();
}
