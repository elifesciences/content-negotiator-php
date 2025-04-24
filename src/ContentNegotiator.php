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
        $header = implode(', ', $this->getHeader($request));
        if (empty($header)) {
            $header = '*';
        }

        $match = $this->negotiator->getBest($header, $priorities);

        if (null === $match) {
            throw new NotAcceptableHttpException(sprintf("Unable to produce '%s', possibilities for %s are: '%s'", $header, $this->header, implode("', '", $priorities)));
        }

        $request->attributes->set($this->attribute, $match);
    }

    /**
     * We need to account for the slightly different APIs of symfony components between v3, v4 and v5+
     *
     * the third argument to HeaderBag::get() being false works below < v5
     * the first argument to HeaderBag::all() works > v4
     *
     * Drop this when dropping support for symfony 3
     */
    private function getHeader(Request $request)
    {
        $headerBagAllMethod = new \ReflectionMethod($request->headers, 'all');
        if ($headerBagAllMethod->getNumberOfParameters() > 0) {
            return $request->headers->all($this->header);
        }
        return $request->headers->get($this->header, null, false);
    }
}
