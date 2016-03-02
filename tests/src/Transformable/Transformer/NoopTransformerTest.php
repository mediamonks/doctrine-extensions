<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class NoopTransformerTest extends \PHPUnit_Framework_TestCase
{
    const VALUE = 'foobar';

    /**
     * @var NoopTransformer
     */
    protected $transformer;

    protected function setUp()
    {
        $this->transformer = new NoopTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals(self::VALUE, $this->transformer->transform(self::VALUE));
    }

    public function testReverseTransform()
    {
        $this->assertEquals(self::VALUE, $this->transformer->reverseTransform(self::VALUE));
    }
}