<?php

namespace test\eLife\ContentNegotiator\Symfony;

use eLife\ContentNegotiator\TypeAttributeValueResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class TypeAttributeValueResolverTest extends TestCase
{
    #[Test]
    public function it_supports_matching_type_and_attribute(): void
    {
        $resolver = new TypeAttributeValueResolver('type', 'attribute');

        $request = new Request();
        $request->attributes->set('attribute', 'value');
        $argument = new ArgumentMetadata('name', 'type', false, false, null);

        $this->assertSame(['value'], [...$resolver->resolve($request, $argument)]);
    }

    #[Test]
    public function it_does_not_support_if_there_is_no_attribute()
    {
        $resolver = new TypeAttributeValueResolver('type', 'attribute');

        $request = new Request();
        $argument = new ArgumentMetadata('name', 'type', false, false, null);

        $this->assertSame([], [...$resolver->resolve($request, $argument)]);
    }

    #[Test]
    public function it_does_not_support_if_the_type_is_different()
    {
        $resolver = new TypeAttributeValueResolver('type', 'attribute');

        $request = new Request();
        $request->attributes->set('attribute', 'value');
        $argument = new ArgumentMetadata('name', 'foo', false, false, null);

        $this->assertSame([], [...$resolver->resolve($request, $argument)]);
    }
}
