<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;
use \Mockery as m;
use PHPUnit\Framework\TestCase;

class TransformerPoolTest extends TestCase
{
    /**
     * @var TransformerPool
     */
    protected $transformerPool;

    /**
     * @var NoopTransformer
     */
    protected $transformer;

    /**
     * @var string
     */
    protected $transformerKey = 'noop';

    protected function setUp(): void
    {
        $this->transformerPool = new TransformerPool();
        $this->transformer = $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface');

        $this->transformerPool->set($this->transformerKey, $this->transformer);
    }

    public function testSetGet()
    {
        $this->assertEquals($this->transformer, $this->transformerPool->get($this->transformerKey));
    }

    public function testExists()
    {
        $this->assertTrue($this->transformerPool->offsetExists($this->transformerKey));
    }

    public function testUnset()
    {
        unset($this->transformerPool[$this->transformerKey]);
        $this->assertFalse($this->transformerPool->offsetExists($this->transformerKey));
    }

    public function testInvalidArgumentExceptionThrownOnNonExistingTransformer()
    {
        $this->expectException('MediaMonks\Doctrine\Exception\InvalidArgumentException');
        $this->transformerPool->get('non_existing_transformer');
    }
}
