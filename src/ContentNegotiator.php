<?php

namespace eLife\ContentNegotiator;

use Negotiation\AbstractNegotiator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

final class ContentNegotiator
{
    private $negotiator;
    private $header;
    private $attribute;

    public function __construct(AbstractNegotiator $negotiator, string $header, string $attribute)
    {
        $this->negotiator = $negotiator;
        $this->header = $header;
        $this->attribute = $attribute;
    }

    public function negotiate(Request $request, array $priorities)
    {
        $header = $request->headers->get($this->header, '*') ?? '*';

        $match = $this->negotiator->getBest($header, $priorities);

        if (null === $match) {
            throw new NotAcceptableHttpException(sprintf("Unable to produce '%s', possibilities for %s are: '%s'", $header, $this->header, implode("', '", $priorities)));
        }

        $request->attributes->set($this->attribute, $match);
    }
}
