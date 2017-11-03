<?php

namespace eLife\ContentNegotiator\Symfony;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class TypeAttributeValueResolver implements ArgumentValueResolverInterface
{
    private $type;
    private $attribute;

    public function __construct(string $type, string $attribute)
    {
        $this->type = $type;
        $this->attribute = $attribute;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $this->type === $argument->getType() && $request->attributes->has($this->attribute);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $request->attributes->get($this->attribute);
    }
}
