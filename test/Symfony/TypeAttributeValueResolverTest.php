<?php

namespace test\eLife\ContentNegotiator\Symfony;

use eLife\ContentNegotiator\Symfony\TypeAttributeValueResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class TypeAttributeValueResolverTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!interface_exists(ArgumentValueResolverInterface::class)) {
            self::markTestSkipped('Requires symfony/http-kernel >= 3.1');
        }
    }

    /**
     * @test
     */
    public function it_supports_matching_type_and_attribute()
    {
        $resolver = new TypeAttributeValueResolver('type', 'attribute');

        $request = new Request();
        $request->attributes->set('attribute', 'value');
        $argument = new ArgumentMetadata('name', 'type', false, false, null);

        $this->assertTrue($resolver->supports($request, $argument));
        $this->assertSame(['value'], iterator_to_array($resolver->resolve($request, $argument)));
    }

    /**
     * @test
     */
    public function it_does_not_support_if_there_is_no_attribute()
    {
        $resolver = new TypeAttributeValueResolver('type', 'attribute');

        $request = new Request();
        $argument = new ArgumentMetadata('name', 'type', false, false, null);

        $this->assertFalse($resolver->supports($request, $argument));
    }

    /**
     * @test
     */
    public function it_does_not_support_if_the_type_is_different()
    {
        $resolver = new TypeAttributeValueResolver('type', 'attribute');

        $request = new Request();
        $request->attributes->set('attribute', 'value');
        $argument = new ArgumentMetadata('name', 'foo', false, false, null);

        $this->assertFalse($resolver->supports($request, $argument));
    }
}
