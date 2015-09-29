<?php

namespace Jitsu\Http;

use Jitsu\Response as r;
use Jitsu\ArrayUtil;

class CurrentResponse extends ResponseBase {

	public function setStatus($version, $code, $reason) {
		return r::setStatus($version, $code, $reason);
	}

	public function setStatusCode($code, $reason = null) {
		if($reason === null) {
			return r::setStatusCode($code);
		} else {
			return parent::setStatusCode($code, $reason);
		}
	}

	public function statusCode() {
		return r::statusCode();
	}

	public function addHeader($name, $value) {
		return r::addHeader($name, $value);
	}

	public function addCookie($name, $value, $attrs = array()) {
		// TODO
		$attrs = array_change_key_case($attrs);
		if(ArrayUtil::hasKey($attrs, 'max-age')) {
			$expires = time() + $attrs['max-age'];
		} else {
			$expires = ArrayUtil::get($attrs, 'expires');
		}
		r::addCookie(
			$name,
			$value,
			$expires,
			ArrayUtil::get($attrs, 'path'),
			ArrayUtil::get($attrs, 'domain')
		);
	}

	public function deleteCookie($name, $attrs) {
		// TODO
	}

	public function redirect($url, $code, $reason = null) {
		if($reason === null) {
			return r::redirect($url, $code);
		} else {
			return parent::redirect($url, $code, $reason);
		}
	}

	public function flushOutputBuffer() {
		return r::flushOutputBuffer();
	}

	public function sentHeaders() {
		return r::sentHeaders();
	}

	public function json($obj, $pretty = false) {
		return r::json($obj, $pretty);
	}

	public function file($path, $content_type) {
		return r::file($path, $content_type);
	}
}
