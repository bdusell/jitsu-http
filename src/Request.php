<?php

namespace Jitsu;

/**
 * Get information about the current HTTP request being processed.
 */
class Request {

	const REQUEST_BODY_STREAM = 'php://input';

	/**
	 * Get the URI's scheme (`http` or `https`).
	 */
	public static function scheme() {
		return $_SERVER['REQUEST_SCHEME'];
	}

	/**
	 * Get the protocol/version indicated in the HTTP request.
	 */
	public static function protocol() {
		return $_SERVER['SERVER_PROTOCOL'];
	}

	/**
	 * Get the value of the `Host` header.
	 */
	public static function host() {
		return $_SERVER['HTTP_HOST'];
	}

	/**
	 * Get the HTTP method used in the request (`GET`, `POST`, etc.).
	 */
	public static function method() {
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Get the full, raw request URI.
	 */
	public static function uri() {
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * Get the query string of the URI.
	 */
	public static function queryString() {
		return $_SERVER['QUERY_STRING'];
	}

	/**
	 * Look up a form-encoded parameter from the request.
	 */
	public static function form($name, $default = null) {
		return \Jitsu\ArrayUtil::get(self::formParams(), $name, $default);
	}

	/**
	 * Get all of the request's form-encoded parameters.
	 */
	public static function formParams() {
		// TODO Handle case where POST size is exceeded.
		// TODO validate content-type
		static $form = null;
		if($form === null) {
			switch(self::method()) {
			case 'GET':
				$form = $_GET;
				break;
			case 'POST':
				$form = $_POST;
				break;
			case 'DELETE':
				/* Note that `parse_str` automatically decodes
				 * the result, so be sure to use the raw
				 * query string. */
				parse_str(self::queryString(), $form);
				break;
			default:
				// PUT, PATCH
				parse_str(self::body(), $form);
				break;
			}
		}
		return $form;
	}

	/**
	 * Look up a request header.
	 *
	 * @param string $name
	 * @return string|null
	 */
	public static function header($name, $default = null) {
		$key = 'HTTP_' . self::_headerToEnv($name);
		return \Jitsu\ArrayUtil::get($_SERVER, $key, $default);
	}

	/**
	 * Get all request headers.
	 *
	 * @return string[]
	 */
	public static function headers() {
		static $headers = null;
		if($headers === null) {
			$headers = self::_getHeaders();
		}
		return $headers;
	}

	private static function _getHeaders() {
		if(function_exists('apache_request_headers')) {
			return apache_request_headers();
		} else {
			return self::_getHeadersFromServer();
		}
	}

	private static function _getHeadersFromServer() {
		$result = array();
		foreach($_SERVER as $k => $v) {
			if(strncmp($k, 'HTTP_', 5) === 0) {
				$name = self::_envToHeader(substr($k, 5));
				$result[$name] = $v;
			}
		}
		return $result;
	}

	private static function _headerToEnv($name) {
		return strtoupper(str_replace('-', '_', $name));
	}

	private static function _envToHeader($name) {
		return strtolower(str_replace('_', '-', $name));
	}

	/**
	 * Look up cookies sent in the request.
	 *
	 * @param string $name
	 * @return string|null
	 */
	public static function cookie($name, $default = null) {
		return \Jitsu\ArrayUtil::get($_COOKIE, $name, $default);
	}

	/**
	 * Get all cookies sent in the request as an array.
	 *
	 * @return string[]
	 */
	public static function cookies() {
		return $_COOKIE;
	}

	/**
	 * Slurp the raw input sent in the request body into a single string.
	 *
	 * The result is cached, so this function may be called more than once.
	 *
	 * @return string
	 */
	public static function body() {
		static $result = null;
		if($result === null) {
			$result = file_get_contents(self::REQUEST_BODY_STREAM);
		}
		return $result;
	}

	/**
	 * Return a file stream handle to the request body.
	 *
	 * @return resource
	 */
	public static function bodyStream() {
		return fopen(self::REQUEST_BODY_STREAM, 'r');
	}

	/**
	 * Look up information about a file sent in a multipart-form request.
	 *
	 * @param string $name
	 * @return array
	 */
	public static function file($name) {
		return \Jitsu\ArrayUtil::get($_FILES, $name);
	}

	/**
	 * Get information about all files sent in a multipart-form request.
	 *
	 * @return array[]
	 */
	public static function files() {
		return $_FILES;
	}

	/**
	 * Save a file uploaded as a multipart-form parameter.
	 *
	 * Saves the file uploaded under the form parameter `$name` to the path
	 * `$dest_path` on the filesystem.
	 *
	 * @param string $name
	 * @param string $dest_path
	 * @throws \RuntimeException Thrown if the file is missing, if there is
	 *                           an error code associated with this file
	 *                           upload, or if it could not be saved.
	 */
	public static function saveFile($name, $dest_path) {
		if(array_key_exists($name, $_FILES)) {
			$info = $_FILES[$name];
			if(($error = $info['error']) === UPLOAD_ERR_OK) {
				if(!move_uploaded_file($info['tmp_name'], $dest_path)) {
					throw new \RuntimeException('unable to save uploaded file');
				}
			} else {
				throw new \RuntimeException(self::fileErrorMessage($error), $error);
			}
		} else {
			throw new \RuntimeException('no file uploaded under parameter "' . $name . '"');
		}
		$info = $_FILES[$name];
	}

	/**
	 * Get the IP address of the remote endpoint.
	 *
	 * @return string
	 */
	public static function originIpAddress() {
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Get the port number of the remote endpoint.
	 *
	 * @return string
	 */
	public static function originPort() {
		return $_SERVER['REMOTE_PORT'];
	}

	/**
	 * Timestamp of the start of the request.
	 *
	 * @return int
	 */
	public static function timestamp() {
		return $_SERVER['REQUEST_TIME'];
	}

	private static function fileErrorMessage($code) {
		switch($code) {
		case UPLOAD_ERR_OK:
			return 'no error';
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			return 'uploaded file is too large';
		case UPLOAD_ERR_PARTIAL:
			return 'incomplete file upload';
		case UPLOAD_ERR_NO_FILE:
			return 'missing file contents';
		/*
		case UPLOAD_ERR_NO_TMP_DIR:
		case UPLOAD_ERR_CANT_WRITE:
		case UPLOAD_ERR_EXTENSION:
		*/
		default:
			return 'internal error';
		}
	}
}
