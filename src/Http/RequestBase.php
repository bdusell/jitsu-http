<?php

namespace Jitsu\Http;

/**
 * An extension of `RequestInterface` which offers some utility methods.
 */
abstract class RequestBase implements RequestInterface {

	/**
	 * Get the full request URL.
	 *
	 * This includes the scheme, host name, path, and query string. This is
	 * in raw form and not URL-decoded.
	 *
	 * @return string
	 */
	public function fullUrl() {
		return $this->getScheme() . '://' . $this->host() . $this->getUri();
	}

	/**
	 * Get the request scheme.
	 *
	 * @return string
	 */
	public function scheme() {
		return $this->getScheme();
	}

	/**
	 * Get the protocol/version string used in the request.
	 *
	 * @return string
	 */
	public function protocol() {
		return $this->getProtocol();
	}

	/**
	 * Get the value of the `Host` header.
	 *
	 * @return string
	 */
	public function host() {
		return $this->getHeader('Host');
	}

	/**
	 * Get the HTTP method used in the request.
	 *
	 * Always returned in upper case (e.g. `'GET'`, `'PUT'`, etc.).
	 *
	 * @return string
	 */
	public function method() {
		return strtoupper($this->getMethod());
	}

	/**
	 * Get the raw URI sent in the request.
	 *
	 * This consists of the path and query string of the request. Note that
	 * this is in raw form and not URL-decoded.
	 *
	 * @return string
	 */
	public function uri() {
		return $this->getUri();
	}

	/**
	 * Get the path part of the request URI.
	 *
	 * Note that this is in raw form and not URL-decoded.
	 *
	 * @return string
	 */
	public function path() {
		/* Note that it is necessary to pass the full URL to
		 * `parse_url`, because `parse_url` can be tricked into
		 * thinking that part of the path is a domain name. */
		return parse_url($this->fullUrl(), PHP_URL_PATH);
	}

	/**
	 * Get the query string of the request URI.
	 *
	 * Note that this is in raw form and not URL-decoded.
	 *
	 * @return string
	 */
	public function queryString() {
		/* Note that it is necessary to pass the full URL to
		 * `parse_url`, because `parse_url` can be tricked into
		 * thinking that part of the path is a domain name. */
		return parse_url($this->fullUrl(), PHP_URL_QUERY);
	}

	/**
	 * Look up a form-encoded parameter from the request.
	 *
	 * @param string $name
	 * @param mixed $default Default value to get if the parameter does not
	 *                       exist.
	 * @return string|null|mixed
	 */
	public function form($name, $default = null) {
		return \Jitsu\ArrayUtil::get($this->formParams(), $name, $default);
	}

	/**
	 * Get all of the form-encoded parameters from the request.
	 *
	 * Returns an array mapping the names of the form-encoded parameters
	 * sent in the request to their values. All keys and values are
	 * decoded. The parameters are taken from the appropriate part of the
	 * request based on the HTTP method used. For `GET`, `DELETE`, etc.
	 * they are parsed from the query string, and otherwise they are
	 * parsed from the request body.
	 *
	 * @return string[]
	 */
	public function formParams() {
		switch($this->method()) {
		case 'GET':
		case 'DELETE':
		case 'HEAD':
		case 'OPTIONS':
		case 'TRACE':
			$query_str = $this->queryString();
			break;
		default:
			// TODO Validate Content-Type
			$query_str = $this->getBody();
		}
		parse_str($query_str, $result);
		return $result;
	}

	/**
	 * Look up a header sent in the request.
	 *
	 * @param string $name Case-insensitive name of the header.
	 * @param mixed $default Default value to get if the header does not
	 *                       exist.
	 * @return string|null|mixed
	 */
	public function header($name, $default = null) {
		$r = $this->getHeader($name);
		return $r === null ? $default : $r;
	}

	/**
	 * Get all headers sent in the request.
	 *
	 * Returns an array mapping the original header names to their values.
	 *
	 * @return string[]
	 */
	public function headers() {
		return $this->getAllHeaders();
	}

	/**
	 * Get the content type of the request.
	 *
	 * @return string|null
	 */
	public function contentType() {
		return $this->getHeader('Content-Type');
	}

	/**
	 * Parse the `Accept` header into a list of acceptable content types.
	 *
	 * Returns an associative array mapping content type strings to their
	 * respective quality ratings, ordered in descending order of quality.
	 *
	 * @return string[]
	 */
	public function acceptableContentTypes() {
		$accept = $this->getHeader('Accept');
		return $accept === null ? array() : self::parseNegotiation($accept);
	}

	/**
	 * Return whether the request will accept a certain content type.
	 *
	 * @param string $content_type
	 * @return bool
	 */
	public function accepts($content_type) {
		$accept = $this->acceptableContentTypes();
		return (
			array_key_exists($content_type, $accept) &&
			$accept[$content_type] > 0
		);
	}

	private static function parseNegotiation($str) {
		$result = array();
		$parts = preg_split('/\s*,\s*/', $str);
		foreach($parts as $part) {
			$matches = null;
			if(preg_match('/^(.*?)\s*(;\s*q=(.*))?$/', $part, $matches)) {
				$type = $matches[1];
				if(array_key_exists(2, $matches) && is_numeric($matches[3])) {
					$quality = (float) $matches[3];
				} else {
					$quality = 1.0;
				}
				$result[$type] = $quality;
			}
		}
		arsort($result);
		return $result;
	}

	/**
	 * Determine the request's most acceptable content type.
	 *
	 * Given an array of acceptable content types, returns the index of the
	 * content type with the highest quality rating in the `Accept`
	 * header. Returns `null` if no content type is acceptable.
	 *
	 * @param string[] $acceptable
	 * @return int|null
	 */
	public function negotiateContentType($acceptable) {
		$accept = $this->acceptableContentTypes();
		$expected = array_flip($acceptable);
		foreach($accept as $pattern => $quality) {
			$regex = self::ctPatternRegex($pattern);
			if($regex === null) {
				if(array_key_exists($pattern, $expected)) {
					return $expected[$pattern];
				}
			} else {
				foreach($acceptable as $i => $type) {
					if(preg_match($regex, $type)) {
						return $i;
					}
				}
			}
		}
	}

	private static function ctPatternRegex($pattern) {
		$wildcard = false;
		$regex = preg_replace_callback(
			'/(\\*)|(.+?)/',
			function($matches) use(&$wildcard) {
				if($matches[1] !== '') {
					$wildcard = true;
					return '[^/]*';
				} else {
					return preg_quote($matches[2], '#');
				}
			},
			$pattern
		);
		if($wildcard) {
			return '#^' . $regex . '$#';
		}
	}

	/**
	 * Alias of `referrer`.
	 *
	 * @see \Jitsu\RequestBase::referrer() Alias of the correctly spelled
	 *                                     `referrer`.
	 *
	 * @return string|null
	 */
	public function referer() {
		return $this->referer();
	}

	/**
	 * Get the HTTP referrer URI or `null` if it was not sent.
	 *
	 * @return string|null
	 */
	public function referrer() {
		return $this->getHeader('Referer');
	}

	/**
	 * Look up a cookie sent in the request.
	 *
	 * Parses and decodes the cookie value.
	 *
	 * @param string $name
	 * @return string|null
	 */
	public function cookie($name, $default = null) {
		return \Jitsu\ArrayUtil::get($this->cookies(), $name, $default);
	}

	/**
	 * Get the cookies sent in the request.
	 *
	 * Parses and decodes the cookie values.
	 *
	 * TODO: This is currently not implemented.
	 *
	 * @return string[]
	 */
	public function cookies() {
		$cookie = $this->getHeader('Cookie');
		return $cookie === null ? array() : (array) self::parseCookies($cookie);
	}

	private static function parseCookies($str) {
		// TODO
		// :(
	}

	/**
	 * Return the request body as a single string.
	 *
	 * @return string
	 */
	public function body() {
		return $this->getBody();
	}

	/**
	 * Return a readable file stream handle to the request body.
	 *
	 * @return resource
	 */
	public function bodyStream() {
		return $this->getBodyStream();
	}

	/**
	 * Get the IP address of the remote endpoint.
	 *
	 * @return string
	 */
	public function originIpAddress() {
		return $this->getOriginIpAddress();
	}

	/**
	 * Get the port number of the remote endpoint.
	 *
	 * @return string
	 */
	public function originPort() {
		return $this->getOriginPort();
	}
}
