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
interesting ones are defined under `Jitsu\Http`.

## API

