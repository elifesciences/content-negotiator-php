<?php

namespace test\eLife\ContentNegotiator;

use eLife\ContentNegotiator\ContentNegotiator;
use Negotiation\Accept;
use Negotiation\AcceptLanguage;
use Negotiation\LanguageNegotiator;
use Negotiation\Negotiator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Traversable;

final class ContentNegotiatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider typeNegotiationProvider
     */
    public function it_negotiates_a_type(string $accept = null, string $expected)
    {
        $contentNegotiator = new ContentNegotiator(new Negotiator(), 'Accept', 'accept_type');

        $request = new Request();
        $request->headers->set('Accept', $accept);

        $contentNegotiator->negotiate($request, ['text/plain', 'text/rtf']);

        $this->assertTrue($request->attributes->has('accept_type'));
        $this->assertEquals(new Accept($expected), $request->attributes->get('accept_type'));
    }

    public function typeNegotiationProvider() : Traversable
    {
        yield 'first type' => ['text/plain', 'text/plain'];
        yield 'second type' => ['text/rtf', 'text/rtf'];
        yield 'prefers first type' => ['text/rtf, text/plain', 'text/plain'];
        yield 'respects q parameter' => ['text/rtf;q=1, text/plain;q=0.1', 'text/rtf'];
        yield 'other type but has fallback' => ['text/html, */*', 'text/plain'];
        yield 'no header' => [null, 'text/plain'];
    }

    /**
     * @test
     * @dataProvider typeNegotiationProvider
     */
    public function it_recognises_multiple_accept_headers()
    {
        $contentNegotiator = new ContentNegotiator(new Negotiator(), 'Accept', 'accept_type');

        $request = new Request();
        $request->headers->set('Accept', 'text/rtf', false);
        $request->headers->set('Accept', 'text/plain', false);

        $contentNegotiator->negotiate($request, ['text/plain', 'text/rtf']);

        $this->assertTrue($request->attributes->has('accept_type'));
        $this->assertEquals(new Accept('text/plain'), $request->attributes->get('accept_type'));
    }

    /**
     * @test
     * @dataProvider languageNegotiationProvider
     */
    public function it_negotiates_other_headers(string $accept = null, string $expected)
    {
        $contentNegotiator = new ContentNegotiator(new LanguageNegotiator(), 'Accept-Language', 'accept_language');

        $request = new Request();
        $request->headers->set('Accept-Language', $accept);

        $contentNegotiator->negotiate($request, ['en', 'fr']);

        $this->assertTrue($request->attributes->has('accept_language'));
        $this->assertEquals(new AcceptLanguage($expected), $request->attributes->get('accept_language'));
    }

    public function languageNegotiationProvider() : Traversable
    {
        yield 'first language' => ['en', 'en'];
        yield 'second language' => ['fr', 'fr'];
        yield 'prefers first language' => ['fr, en', 'en'];
        yield 'respects q parameter' => ['fr;q=1, en;q=0.1', 'fr'];
        yield 'other type but has fallback' => ['de, *', 'en'];
        yield 'no header' => [null, 'en'];
    }

    /**
     * @test
     */
    public function it_rejects_requests_that_cannot_be_negotiated()
    {
        $contentNegotiator = new ContentNegotiator(new Negotiator(), 'Accept', 'accept_type');

        $request = new Request();
        $request->headers->set('Accept', 'text/html');

        $this->expectException(NotAcceptableHttpException::class);
        $this->expectExceptionMessage("Unable to produce 'text/html', possibilities for Accept are: 'text/plain', 'text/rtf'");

        $contentNegotiator->negotiate($request, ['text/plain', 'text/rtf']);
    }
}
