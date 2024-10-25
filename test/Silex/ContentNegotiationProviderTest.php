<?php

namespace test\eLife\ContentNegotiator\Silex;

use Closure;
use eLife\ContentNegotiator\ContentNegotiator;
use eLife\ContentNegotiator\Silex\ContentNegotiationProvider;
use Negotiation\Accept;
use Negotiation\AcceptCharset;
use Negotiation\AcceptEncoding;
use Negotiation\AcceptLanguage;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Traversable;

final class ContentNegotiationProviderTest extends WebTestCase
{
    /** @var Application */
    protected $app;

    /**
     * @test
     * @dataProvider serviceProvider
     */
    public function it_creates_services(string $id, string $class)
    {
        $this->assertArrayHasKey($id, $this->app);
        $this->assertInstanceOf($class, $this->app[$id]);
    }

    public function serviceProvider() : Traversable
    {
        $services = [
            'content_negotiator.accept' => ContentNegotiator::class,
            'content_negotiator.accept_charset' => ContentNegotiator::class,
            'content_negotiator.accept_encoding' => ContentNegotiator::class,
            'content_negotiator.accept_language' => ContentNegotiator::class,
            'negotiate.accept' => Closure::class,
            'negotiate.accept_charset' => Closure::class,
            'negotiate.accept_encoding' => Closure::class,
            'negotiate.accept_language' => Closure::class,
        ];

        foreach ($services as $id => $type) {
            yield $id => [$id, $type];
        }
    }

    /**
     * @test
     * @dataProvider acceptProvider
     */
    public function it_negotiates_the_accept_header(string $header = null, int $statusCode, string $content = null)
    {
        $client = $this->createClient();

        $path = interface_exists(ArgumentValueResolverInterface::class) ? '/accept-type' : '/accept';
        $client->request('GET', $path, [], [], ['HTTP_ACCEPT' => $header]);
        $response = $client->getResponse();

        $this->assertSame($statusCode, $response->getStatusCode());
        if ($content) {
            $this->assertSame($content, $response->getContent());
        }
    }

    public function acceptProvider() : Traversable
    {
        yield 'first type' => ['text/plain', 200, 'Accept text/plain'];
        yield 'second type' => ['text/rtf', 200, 'Accept text/rtf'];
        yield 'prefers first type' => ['text/rtf, text/plain', 200, 'Accept text/plain'];
        yield 'respects q parameter' => ['text/rtf;q=1, text/plain;q=0.1', 200, 'Accept text/rtf'];
        yield 'other type but has fallback' => ['text/html, */*', 200, 'Accept text/plain'];
        yield 'no header' => [null, 200, 'Accept text/plain'];
        yield 'not matching' => ['text/html', 406];
    }

    /**
     * @test
     * @dataProvider acceptCharsetProvider
     */
    public function it_negotiates_the_accept_charset_header(string $header = null, int $statusCode, string $content = null)
    {
        $client = $this->createClient();

        $path = interface_exists(ArgumentValueResolverInterface::class) ? '/accept-charset-type' : '/accept-charset';
        $client->request('GET', $path, [], [], ['HTTP_ACCEPT_CHARSET' => $header]);
        $response = $client->getResponse();

        $this->assertSame($statusCode, $response->getStatusCode());
        if ($content) {
            $this->assertSame($content, $response->getContent());
        }
    }

    public function acceptCharsetProvider() : Traversable
    {
        yield 'first type' => ['utf-8', 200, 'AcceptCharset utf-8'];
        yield 'second type' => ['iso-8859-1', 200, 'AcceptCharset iso-8859-1'];
        yield 'prefers first type' => ['iso-8859-1, utf-8', 200, 'AcceptCharset utf-8'];
        yield 'respects q parameter' => ['iso-8859-1;q=1, utf-8;q=0.1', 200, 'AcceptCharset iso-8859-1'];
        yield 'other type but has fallback' => ['us-ascii, *', 200, 'AcceptCharset utf-8'];
        yield 'no header' => [null, 200, 'AcceptCharset utf-8'];
        yield 'not matching' => ['us-ascii', 406];
    }

    /**
     * @test
     * @dataProvider acceptEncodingProvider
     */
    public function it_negotiates_the_accept_encoding_header(string $header = null, int $statusCode, string $content = null)
    {
        $client = $this->createClient();

        $path = interface_exists(ArgumentValueResolverInterface::class) ? '/accept-encoding-type' : '/accept-encoding';
        $client->request('GET', $path, [], [], ['HTTP_ACCEPT_ENCODING' => $header]);
        $response = $client->getResponse();

        $this->assertSame($statusCode, $response->getStatusCode());
        if ($content) {
            $this->assertSame($content, $response->getContent());
        }
    }

    public function acceptEncodingProvider() : Traversable
    {
        yield 'first type' => ['gzip', 200, 'AcceptEncoding gzip'];
        yield 'second type' => ['compress', 200, 'AcceptEncoding compress'];
        yield 'prefers first type' => ['compress, gzip', 200, 'AcceptEncoding gzip'];
        yield 'respects q parameter' => ['compress;q=1, gzip;q=0.1', 200, 'AcceptEncoding compress'];
        yield 'other type but has fallback' => ['deflate, *', 200, 'AcceptEncoding gzip'];
        yield 'no header' => [null, 200, 'AcceptEncoding gzip'];
        yield 'not matching' => ['deflate', 406];
    }

    /**
     * @test
     * @dataProvider acceptLanguageProvider
     */
    public function it_negotiates_the_accept_language_header(string $header = null, int $statusCode, string $content = null)
    {
        $client = $this->createClient();

        $path = interface_exists(ArgumentValueResolverInterface::class) ? '/accept-language-type' : '/accept-language';
        $client->request('GET', $path, [], [], ['HTTP_ACCEPT_LANGUAGE' => $header]);
        $response = $client->getResponse();

        $this->assertSame($statusCode, $response->getStatusCode());
        if ($content) {
            $this->assertSame($content, $response->getContent());
        }
    }

    public function acceptLanguageProvider() : Traversable
    {
        yield 'first type' => ['en', 200, 'AcceptLanguage en'];
        yield 'second type' => ['fr', 200, 'AcceptLanguage fr'];
        yield 'prefers first type' => ['fr, en', 200, 'AcceptLanguage en'];
        yield 'respects q parameter' => ['fr;q=1, en;q=0.1', 200, 'AcceptLanguage fr'];
        yield 'other type but has fallback' => ['de, *', 200, 'AcceptLanguage en'];
        yield 'no header' => [null, 200, 'AcceptLanguage en'];
        yield 'not matching' => ['de', 406];
    }

    public function createApplication() : Application
    {
        $app = new Application();
        $app->register(new ContentNegotiationProvider());

        $app['debug'] = true;

        $app->get('/accept-type', function (Accept $accept) {
            return new Response("Accept {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept']('text/plain', 'text/rtf'));

        $app->get('/accept', function (Request $request) {
            $accept = $request->attributes->get(ContentNegotiationProvider::ATTRIBUTE_ACCEPT, new Accept('unknown/value'));

            return new Response("Accept {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept']('text/plain', 'text/rtf'));

        $app->get('/accept-charset-type', function (AcceptCharset $accept) {
            return new Response("AcceptCharset {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept_charset']('utf-8', 'iso-8859-1'));

        $app->get('/accept-charset', function (Request $request) {
            $accept = $request->attributes->get(ContentNegotiationProvider::ATTRIBUTE_ACCEPT_CHARSET, new AcceptCharset('unknown'));

            return new Response("AcceptCharset {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept_charset']('utf-8', 'iso-8859-1'));

        $app->get('/accept-encoding-type', function (AcceptEncoding $accept) {
            return new Response("AcceptEncoding {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept_encoding']('gzip', 'compress'));

        $app->get('/accept-encoding', function (Request $request) {
            $accept = $request->attributes->get(ContentNegotiationProvider::ATTRIBUTE_ACCEPT_ENCODING, new AcceptEncoding('unknown'));

            return new Response("AcceptEncoding {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept_encoding']('gzip', 'compress'));

        $app->get('/accept-language-type', function (AcceptLanguage $accept) {
            return new Response("AcceptLanguage {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept_language']('en', 'fr'));

        $app->get('/accept-language', function (Request $request) {
            $accept = $request->attributes->get(ContentNegotiationProvider::ATTRIBUTE_ACCEPT_LANGUAGE, new AcceptLanguage('unknown'));

            return new Response("AcceptLanguage {$accept->getNormalizedValue()}");
        })->before($app['negotiate.accept_language']('en', 'fr'));

        $app->boot();
        $app->flush();

        return $app;
    }
}
