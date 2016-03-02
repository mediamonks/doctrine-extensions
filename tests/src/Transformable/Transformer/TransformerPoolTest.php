<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class TransformerPoolTest extends \PHPUnit_Framework_TestCase
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

    protected function setUp()
    {
        $this->transformerPool = new TransformerPool();
        $this->transformer = new NoopTransformer();

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
}