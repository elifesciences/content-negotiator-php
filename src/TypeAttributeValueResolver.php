<?php

namespace eLife\ContentNegotiator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class TypeAttributeValueResolver implements ValueResolverInterface
{
    private $type;
    private $attribute;

    public function __construct(string $type, string $attribute)
    {
        $this->type = $type;
        $this->attribute = $attribute;
    }


    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($this->type !== $argument->getType() || ! $request->attributes->has($this->attribute)) {
            return;
        }

        yield $request->attributes->get($this->attribute);
    }
}
