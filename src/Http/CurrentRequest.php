<?php

namespace Jitsu\Http;

use \Jitsu\Request as r;

class CurrentRequest extends RequestBase {

	public function getMethod() {
		return r::method();
	}

	public function getUri() {
		return r::uri();
	}

	public function getProtocol() {
		return r::protocol();
	}

	public function getScheme() {
		return r::scheme();
	}

	public function getHeader($name) {
		return r::header($name);
	}

	public function getAllHeaders() {
		return r::headers();
	}

	public function getBody() {
		return r::body();
	}

	public function getBodyStream() {
		return r::bodyStream();
	}

	public function getOriginIpAddress() {
		return r::originIpAddress();
	}

	public function getOriginPort() {
		return r::originPort();
	}

	public function method() {
		static $result = null;
		if($result === null) {
			$result = parent::method();
		}
		return $result;
	}

	public function path() {
		static $result = null;
		if($result === null) {
			$result = parent::path();
		}
		return $result;
	}

	public function queryString() {
		return r::queryString();
	}

	public function form($name, $default = null) {
		return r::form($name, $default);
	}

	public function formParams() {
		return r::formParams();
	}

	public function cookie($name, $default = null) {
		return r::cookie($name, $default);
	}

	public function cookies() {
		return r::cookies();
	}

	public function originIpAddress() {
		return r::originIPAddress();
	}

	public function originPort() {
		return r::originPort();
	}

	public function timestamp() {
		return r::timestamp();
	}
}
