<?php

namespace eLife\ContentNegotiator\Silex;

use eLife\ContentNegotiator\ContentNegotiator;
use eLife\ContentNegotiator\Symfony\TypeAttributeValueResolver;
use Negotiation\Accept;
use Negotiation\AcceptCharset;
use Negotiation\AcceptEncoding;
use Negotiation\AcceptLanguage;
use Negotiation\CharsetNegotiator;
use Negotiation\EncodingNegotiator;
use Negotiation\LanguageNegotiator;
use Negotiation\Negotiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

final class ContentNegotiationProvider implements ServiceProviderInterface
{
    const ATTRIBUTE_ACCEPT = '_accept';
    const ATTRIBUTE_ACCEPT_CHARSET = '_accept_charset';
    const ATTRIBUTE_ACCEPT_ENCODING = '_accept_encoding';
    const ATTRIBUTE_ACCEPT_LANGUAGE = '_accept_language';

    public function register(Container $app)
    {
        $app['content_negotiator.accept'] = function () use ($app) {
            return new ContentNegotiator(new Negotiator(), 'Accept', self::ATTRIBUTE_ACCEPT);
        };

        $app['content_negotiator.accept_charset'] = function () use ($app) {
            return new ContentNegotiator(new CharsetNegotiator(), 'Accept-Charset', self::ATTRIBUTE_ACCEPT_CHARSET);
        };

        $app['content_negotiator.accept_encoding'] = function () use ($app) {
            return new ContentNegotiator(new EncodingNegotiator(), 'Accept-Encoding', self::ATTRIBUTE_ACCEPT_ENCODING);
        };

        $app['content_negotiator.accept_language'] = function () use ($app) {
            return new ContentNegotiator(new LanguageNegotiator(), 'Accept-Language', self::ATTRIBUTE_ACCEPT_LANGUAGE);
        };

        $app['negotiate.accept'] = function () {
            return function (string ...$types) {
                return function (Request $request, Container $app) use ($types) {
                    $app['content_negotiator.accept']->negotiate($request, $types);
                };
            };
        };

        $app['negotiate.accept_charset'] = function () {
            return function (string ...$types) {
                return function (Request $request, Container $app) use ($types) {
                    $app['content_negotiator.accept_charset']->negotiate($request, $types);
                };
            };
        };

        $app['negotiate.accept_encoding'] = function () {
            return function (string ...$types) {
                return function (Request $request, Container $app) use ($types) {
                    $app['content_negotiator.accept_encoding']->negotiate($request, $types);
                };
            };
        };

        $app['negotiate.accept_language'] = function () {
            return function (string ...$types) {
                return function (Request $request, Container $app) use ($types) {
                    $app['content_negotiator.accept_language']->negotiate($request, $types);
                };
            };
        };

        if (interface_exists(ArgumentValueResolverInterface::class)) {
            // Support for symfony/http-kernel 3.1+
            $app->extend('argument_value_resolvers', function (array $resolvers) {
                $resolvers[] = new TypeAttributeValueResolver(Accept::class, self::ATTRIBUTE_ACCEPT);
                $resolvers[] = new TypeAttributeValueResolver(AcceptCharset::class, self::ATTRIBUTE_ACCEPT_CHARSET);
                $resolvers[] = new TypeAttributeValueResolver(AcceptEncoding::class, self::ATTRIBUTE_ACCEPT_ENCODING);
                $resolvers[] = new TypeAttributeValueResolver(AcceptLanguage::class, self::ATTRIBUTE_ACCEPT_LANGUAGE);

                return $resolvers;
            });
        }
    }
}
