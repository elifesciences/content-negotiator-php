eLife content negotiator for PHP
================================

[![Build Status](https://alfred.elifesciences.org/buildStatus/icon?job=library-content-negotiator-php)](https://alfred.elifesciences.org/job/library-content-negotiator-php/)

This library provides a wrapper around [Negotiation](https://github.com/willdurand/Negotiation).

Dependencies
------------

* [Composer](https://getcomposer.org/)
* PHP 7

Installation
------------

`composer require elife/content-negotiator`

Set up
------

### Silex

```php
use eLife\ContentNegotiator\Silex\ContentNegotiationProvider;
use Negotiation\Accept;

$app->register(new ContentNegotiationProvider());

$app->get('/path', function (Accept $accept) {
    return new Response("Negotiated {$accept->getNormalizedValue()}");
})->before($app['negotiate.accept']('text/plain', 'text/rtf'));
```

When using `symfony/http-kernel` 3.1+, you can type-hint an argument on your controller with one of the following types and the result of the negotiation will be used:

| Negotiator                          | Type                         |
| ----------------------------------- | ---------------------------- |
| `$app['negotiate.accept']`          | `Negotiation\Accept`         |
| `$app['negotiate.accept_charset']`  | `Negotiation\AcceptCharset`  |
| `$app['negotiate.accept_encoding']` | `Negotiation\AcceptEncoding` |
| `$app['negotiate.accept_language']` | `Negotiation\AcceptLanguage` |

Running the tests
-----------------

`vendor/bin/phpunit`
