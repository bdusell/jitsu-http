Jitsu HTTP Requests and Responses
---------------------------------

This package abstracts the various ways PHP has to access information about the
incoming HTTP request and to build the outgoing HTTP response in behind a
common, object-oriented interface. Unlike typical PHP scripts which access
`$_GET`, `$_SERVER`, etc. directly, applications written to use this API can
easily be tested with mock HTTP request and response objects.
