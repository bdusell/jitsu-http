jitsu/http
----------

This package defines an abstract, object-oriented interface for interacting
with HTTP requests and responses, and implements this interface for the request
and response data which are traditionally accessible by the PHP script through
superglobal variables, global functions, etc. This makes it possible to
decouple application code from the specifics of where requests come from and
where responses go, enabling dependency injection and unit testing.

Do also take a look at [PSR-7](http://www.php-fig.org/psr/psr-7/), which is
designed to meet the same goals and, obviously, has more community traction.

This package is part of [Jitsu](https://github.com/bdusell/jitsu).

## Installation

Install this package with [Composer](https://getcomposer.org/):

```sh
composer require jitsu/http
```

## Namespace

All classes and interfaces are defined under the namespace `Jitsu`. The
main ones are defined under `Jitsu\Http`.

## API

### interface Jitsu\\Http\\RequestInterface

An abstract interface to an HTTP request.

#### $request\_interface->getMethod()

Get the HTTP method used in the request.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_interface->getUri()

Get the raw URI sent in the request.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_interface->getProtocol()

Get the protocol/version string used in the request.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_interface->getScheme()

Get the scheme used in the request (`http` or `https`).

|   | Type |
|---|------|
| returns | `string` |

#### $request\_interface->getHeader($name)

Look up a header sent in the request.

Returns the value of the header with the given name
(case-insensitive), or `null` if the header was not sent.

|   | Type |
|---|------|
| **`$name`** | `string` |
| returns | `string|null` |

#### $request\_interface->getAllHeaders()

Get all of the headers sent in the request.

Returns an array mapping the original header names to their values.

|   | Type |
|---|------|
| returns | `string[]` |

#### $request\_interface->getBody()

Get the request body as a single string.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_interface->getBodyStream()

Get a readable file stream handle to the request body.

|   | Type |
|---|------|
| returns | `resource` |

#### $request\_interface->getOriginIpAddress()

Get the IP address of the remote endpoint.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_interface->getOriginPort()

Get the port number of the remote endpoint.

|   | Type |
|---|------|
| returns | `string` |

### interface Jitsu\\Http\\ResponseInterface

An abstract interface to an HTTP response.

#### $response\_interface->setStatus($version, $code, $reason)

Set the HTTP status line of the response.

|   | Type | Description |
|---|------|-------------|
| **`$version`** | `string` | An HTTP version string. The modern version string is `HTTP/1.1`. |
| **`$code`** | `int|string` | An HTTP status code. |
| **`$reason`** | `string` | A reason string describing the status code. |

#### $response\_interface->addHeader($name, $value)

Send an HTTP header in the response.

Does not override previously sent headers with the same name.

|   | Type |
|---|------|
| **`$name`** | `string` |
| **`$value`** | `string` |

#### $response\_interface->flushOutputBuffer()

Flush the PHP output buffer to the body of the response.

#### $response\_interface->sentHeaders()

Determine whether the headers were sent.

If the headers were already sent, they may no longer be modified.

|   | Type |
|---|------|
| returns | `bool` |

### class Jitsu\\Http\\RequestBase

Implements `RequestInterface`.

An extension of `RequestInterface` which offers some utility methods.

#### $request\_base->fullUrl()

Get the full request URL.

This includes the scheme, host name, path, and query string. This is
in raw form and not URL-decoded.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->scheme()

Get the request scheme.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->protocol()

Get the protocol/version string used in the request.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->host()

Get the value of the `Host` header.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->method()

Get the HTTP method used in the request.

Always returned in upper case (e.g. `'GET'`, `'PUT'`, etc.).

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->uri()

Get the raw URI sent in the request.

This consists of the path and query string of the request. Note that
this is in raw form and not URL-decoded.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->path()

Get the path part of the request URI.

Note that this is in raw form and not URL-decoded.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->queryString()

Get the query string of the request URI.

Note that this is in raw form and not URL-decoded.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->form($name, $default = null)

Look up a form-encoded parameter from the request.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` |  |
| **`$default`** | `mixed` | Default value to get if the parameter does not exist. |
| returns | `string|null|mixed` |  |

#### $request\_base->formParams()

Get all of the form-encoded parameters from the request.

Returns an array mapping the names of the form-encoded parameters
sent in the request to their values. All keys and values are
decoded. The parameters are taken from the appropriate part of the
request based on the HTTP method used. For `GET`, `DELETE`, etc.
they are parsed from the query string, and otherwise they are
parsed from the request body.

|   | Type |
|---|------|
| returns | `string[]` |

#### $request\_base->header($name, $default = null)

Look up a header sent in the request.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` | Case-insensitive name of the header. |
| **`$default`** | `mixed` | Default value to get if the header does not exist. |
| returns | `string|null|mixed` |  |

#### $request\_base->headers()

Get all headers sent in the request.

Returns an array mapping the original header names to their values.

|   | Type |
|---|------|
| returns | `string[]` |

#### $request\_base->contentType()

Get the content type of the request.

|   | Type |
|---|------|
| returns | `string|null` |

#### $request\_base->acceptableContentTypes()

Parse the `Accept` header into a list of acceptable content types.

Returns an associative array mapping content type strings to their
respective quality ratings, ordered in descending order of quality.

|   | Type |
|---|------|
| returns | `string[]` |

#### $request\_base->accepts($content\_type)

Return whether the request will accept a certain content type.

|   | Type |
|---|------|
| **`$content_type`** | `string` |
| returns | `bool` |

#### $request\_base->negotiateContentType($acceptable)

Determine the request's most acceptable content type.

Given an array of acceptable content types, returns the index of the
content type with the highest quality rating in the `Accept`
header. Returns `null` if no content type is acceptable.

|   | Type |
|---|------|
| **`$acceptable`** | `string[]` |
| returns | `int|null` |

#### $request\_base->referer()

Alias of `referrer`.

Alias of the correctly spelled `referrer`. See `\Jitsu\RequestBase::referrer()`.

|   | Type |
|---|------|
| returns | `string|null` |

#### $request\_base->referrer()

Get the HTTP referrer URI or `null` if it was not sent.

|   | Type |
|---|------|
| returns | `string|null` |

#### $request\_base->cookie($name, $default = null)

Look up a cookie sent in the request.

Parses and decodes the cookie value.

|   | Type |
|---|------|
| **`$name`** | `string` |
| returns | `string|null` |

#### $request\_base->cookies()

Get the cookies sent in the request.

Parses and decodes the cookie values.

TODO: This is currently not implemented.

|   | Type |
|---|------|
| returns | `string[]` |

#### $request\_base->body()

Return the request body as a single string.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->bodyStream()

Return a readable file stream handle to the request body.

|   | Type |
|---|------|
| returns | `resource` |

#### $request\_base->originIpAddress()

Get the IP address of the remote endpoint.

|   | Type |
|---|------|
| returns | `string` |

#### $request\_base->originPort()

Get the port number of the remote endpoint.

|   | Type |
|---|------|
| returns | `string` |

### class Jitsu\\Http\\ResponseBase

Implements `ResponseInterface`.

An extension of `ResponseInterface` which offers some utility methods.

#### $response\_base->setStatusCode($code, $reason = '')

Set the status code of the response.

|   | Type | Description |
|---|------|-------------|
| **`$code`** | `int|string` | HTTP status code. |
| **`$reason`** | `string` | An optional reason string. If not given, a default is used. |

#### abstract public function statusCode()

Get the current status code set on this response.

#### $response\_base->setContentType($type)

Set the content type of the response.

|   | Type |
|---|------|
| **`$type`** | `string` |

#### $response\_base->addCookie($name, $value, $attrs)

Add a cookie to the response.

TODO: This has yet to be implemented.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` |  |
| **`$value`** | `string` |  |
| **`$attrs`** | `(string|bool)[]` | An array of attributes to assign to the cookie. If an attribute value is a boolean, the value determines whether to include the attribute name in the cookie (e.g. for `Secure` or `HttpOnly`). |

#### $response\_base->redirect($url, $code, $reason = '')

Issue a redirect to another URL.

Note that this does NOT exit the current process. Keep in mind that
relying on the client to respect this header and close the
connection for you is potentially a huge security hole.

The response code is also set here. The response code should be a
3XX code.

#### $response\_base->startOutputBuffering()

Start buffering PHP output.

#### $response\_base->clearOutputBuffer()

Discard the contents of the PHP output buffer.

### class Jitsu\\Http\\CurrentRequest

Extends `RequestBase`.

A sub-class of `RequestBase` and implementation of `RequestInterface` for
the current HTTP request.

#### $current\_request->getMethod()

#### $current\_request->getUri()

#### $current\_request->getProtocol()

#### $current\_request->getScheme()

#### $current\_request->getHeader($name)

#### $current\_request->getAllHeaders()

#### $current\_request->getBody()

#### $current\_request->getBodyStream()

#### $current\_request->getOriginIpAddress()

#### $current\_request->getOriginPort()

#### $current\_request->method()

#### $current\_request->path()

#### $current\_request->queryString()

#### $current\_request->form($name, $default = null)

#### $current\_request->formParams()

#### $current\_request->cookie($name, $default = null)

#### $current\_request->cookies()

#### $current\_request->originIpAddress()

#### $current\_request->originPort()

#### $current\_request->timestamp()

### class Jitsu\\Http\\CurrentResponse

Extends `ResponseBase`.

A sub-class of `ResponseBase` and implementation of `ResponseInterface` for
the current HTTP response.

#### $current\_response->setStatus($version, $code, $reason)

#### $current\_response->setStatusCode($code, $reason = null)

#### $current\_response->statusCode()

#### $current\_response->addHeader($name, $value)

#### $current\_response->addCookie($name, $value, $attrs = array()

#### $current\_response->deleteCookie($name, $attrs)

#### $current\_response->redirect($url, $code, $reason = null)

#### $current\_response->flushOutputBuffer()

#### $current\_response->sentHeaders()

### class Jitsu\\Request

Get information about the current HTTP request being processed.

The class `\Jitsu\Http\CurrentRequest` provides the same information through
an object-oriented interface. It is recommended to use that instead.

#### Request::scheme()

Get the URI's scheme (`http` or `https`).

|   | Type |
|---|------|
| returns | `string` |

#### Request::protocol()

Get the protocol/version indicated in the HTTP request.

|   | Type |
|---|------|
| returns | `string` |

#### Request::host()

Get the value of the `Host` header.

|   | Type |
|---|------|
| returns | `string` |

#### Request::method()

Get the HTTP method used in the request (`GET`, `POST`, etc.).

|   | Type |
|---|------|
| returns | `string` |

#### Request::uri()

Get the full, raw request URI.

|   | Type |
|---|------|
| returns | `string` |

#### Request::queryString()

Get the query string of the URI.

|   | Type |
|---|------|
| returns | `string` |

#### Request::form($name, $default = null)

Look up a form-encoded parameter from the request.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` |  |
| **`$default`** | `mixed` | A default value to get if the parameter does not exist. |
| returns | `string|null|mixed` |  |

#### Request::formParams()

Get all of the request's form-encoded parameters.

|   | Type |
|---|------|
| returns | `string[]` |

#### Request::header($name, $default = null)

Look up an HTTP header in the request.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` |  |
| **`$default`** | `mixed` | Default value to get if the header does not exist. |
| returns | `string|null|mixed` |  |

#### Request::headers()

Get all HTTP headers sent with the request.

If PHP is running through Apache (the function
`apache_request_header` is available), this should return the
header names in their original case. Otherwise, they will be in
lower case.

|   | Type |
|---|------|
| returns | `string[]` |

#### Request::cookie($name, $default = null)

Look up cookies sent in the request.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` |  |
| **`$default`** | `mixed` | Default value to get if the cookie does not exist. |
| returns | `string|null|mixed` |  |

#### Request::cookies()

Get all cookies sent in the request as an array.

|   | Type |
|---|------|
| returns | `string[]` |

#### Request::body()

Slurp the raw input sent in the request body into a single string.

The result is cached, so this function may be called more than once.

|   | Type |
|---|------|
| returns | `string` |

#### Request::bodyStream()

Return a file stream handle to the request body.

|   | Type |
|---|------|
| returns | `resource` |

#### Request::file($name)

Look up information about a file sent in a multipart-form request.

|   | Type |
|---|------|
| **`$name`** | `string` |
| returns | `array` |

#### Request::files()

Get information about all files sent in a multipart-form request.

|   | Type |
|---|------|
| returns | `array[]` |

#### Request::saveFile($name, $dest\_path)

Save a file uploaded as a multipart-form parameter.

Saves the file uploaded under the form parameter `$name` to the path
`$dest_path` on the filesystem.

|   | Type | Description |
|---|------|-------------|
| **`$name`** | `string` |  |
| **`$dest_path`** | `string` |  |
| throws | `\RuntimeException` | Thrown if the file is missing, if there is an error code associated with this file upload, or if it could not be saved. |

#### Request::originIpAddress()

Get the IP address of the remote endpoint.

|   | Type |
|---|------|
| returns | `string` |

#### Request::originPort()

Get the port number of the remote endpoint.

|   | Type |
|---|------|
| returns | `string` |

#### Request::timestamp()

Timestamp of the start of the request.

|   | Type |
|---|------|
| returns | `int` |

### class Jitsu\\Response

Utilities for building the current HTTP response about to be sent.

The class `\Jitsu\Http\CurrentResponse` provides the same capabilities
through an object-oriented interface. It is recommended to use that instead.

#### Response::setStatus($version, $code, $reason)

Set the response status line.

Note that this is mutually exclusive with `code`.

|   | Type |
|---|------|
| **`$version`** | `string` |
| **`$code`** | `int|string` |
| **`$reason`** | `string` |

#### Response::setStatusCode($code)

Set the response code.

Automatically sets an appropriate reason string.

Note that this is mutually exclusive with `status`.

|   | Type |
|---|------|
| **`$code`** | `int|string` |

#### Response::statusCode()

Get the currently set response code.

|   | Type |
|---|------|
| returns | `int` |

#### Response::addHeader($name, $value)

Set a header in the response.

Must be called before output is written, just like PHP `header`.

Does not override previously sent header with the same name.

|   | Type |
|---|------|
| **`$name`** | `string` |
| **`$value`** | `string` |

#### Response::addCookie($name, $value, $expires = null, $path = null, $domain = null)

Set a cookie in the response.

|   | Type |
|---|------|
| **`$name`** | `string` |
| **`$value`** | `string` |
| **`$expires`** | `int|null` |
| **`$path`** | `string|null` |
| **`$domain`** | `string|null` |

#### Response::deleteCookie($name, $domain = null, $path = null)

Indicate in the response to delete a cookie.

|   | Type |
|---|------|
| **`$name`** | `string` |
| **`$domain`** | `string|null` |
| **`$path`** | `string|null` |

#### Response::redirect($url, $code)

Issue an HTTP redirect.

|   | Type |
|---|------|
| **`$url`** | `string` |
| **`$code`** | `int|string` |

#### Response::startOutputBuffering()

Start buffering PHP output.

#### Response::flushOutputBuffer()

Flush the PHP output buffer and stop buffering.

#### Response::clearOutputBuffer()

Discard the contents of the PHP output buffer and stop buffering.

#### Response::sentHeaders()

Determine whether the headers were sent.

If the headers were already sent, they may no longer be modified.

|   | Type |
|---|------|
| returns | `bool` |

#### Response::bodyStream()

Get a handle to a writable file stream to the response body.

|   | Type |
|---|------|
| returns | `resource` |

