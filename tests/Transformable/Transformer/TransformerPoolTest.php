<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Throwable;

class TransformerPoolTest extends TestCase
{
    protected TransformerPool $transformerPool;
    protected TransformerInterface $transformer;
    protected string $transformerKey = 'noop';

    protected function setUp(): void
    {
        $this->transformerPool = new TransformerPool();
        $this->transformer = m::mock(TransformerInterface::class);

        try {
            $this->transformerPool->set($this->transformerKey, $this->transformer);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $this->assertEquals($this->transformer, $this->transformerPool->get($this->transformerKey));
    }

    public function testExists(): void
    {
        $this->assertTrue($this->transformerPool->offsetExists($this->transformerKey));
    }

    public function testUnset(): void
    {
        unset($this->transformerPool[$this->transformerKey]);
        $this->assertFalse($this->transformerPool->offsetExists($this->transformerKey));
    }

    public function testInvalidArgumentExceptionThrownOnNonExistingTransformer(): void
    {
        $this->expectException('MediaMonks\Doctrine\Exception\InvalidArgumentException');
        $this->transformerPool->get('non_existing_transformer');
    }
}
