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

$app->register(new ContentNegotiationProvider());

$app->get('/path', function (Accept $accept) {
    return new Response("Negotiated {$accept->getNormalizedValue()}");
})->before($app['negotiate.accept']('text/plain', 'text/rtf'));
```

Running the tests
-----------------

`vendor/bin/phpunit`
