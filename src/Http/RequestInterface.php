<?php

namespace Jitsu\Http;

interface RequestInterface {

	/**
	 * Get the HTTP method used in the request.
	 *
	 * @return string
	 */
	public function getMethod();

	/**
	 * Get the raw URI sent in the request.
	 *
	 * @return string
	 */
	public function getUri();

	/**
	 * Get the protocol/version string used in the request.
	 *
	 * @return string
	 */
	public function getProtocol();

	/**
	 * Get the scheme used in the request (`http` or `https`).
	 *
	 * @return string
	 */
	public function getScheme();

	/**
	 * Look up a header sent in the request.
	 * 
	 * Returns the value of the header with the given name
	 * (case-insensitive), or `null` if the header was not sent.
	 *
	 * @param string $name
	 * @return string|null
	 */
	public function getHeader($name);

	/**
	 * Get all of the headers sent in the request.
	 *
	 * Returns an array mapping the original header names to their values.
	 *
	 * @return string[]
	 */
	public function getAllHeaders();

	/**
	 * Get the request body as a single string.
	 *
	 * @return string
	 */
	public function getBody();

	/**
	 * Get a readable file stream handle to the request body.
	 *
	 * @return resource
	 */
	public function getBodyStream();

	/**
	 * Get the IP address of the remote endpoint.
	 *
	 * @return string
	 */
	public function getOriginIpAddress();

	/**
	 * Get the port number of the remote endpoint.
	 *
	 * @return string
	 */
	public function getOriginPort();
}
