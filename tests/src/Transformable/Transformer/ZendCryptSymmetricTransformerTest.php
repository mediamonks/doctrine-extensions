<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Mockery as m;

class ZendCryptSymmetricTransformerTest extends \PHPUnit_Framework_TestCase
{
    const VALUE = 'foobar';
    const VALUE_ENCRYPTED = 'foobar_encrypted';

    /**
     * @var NoopTransformer
     */
    protected $transformer;

    protected function setUp()
    {
        $crypt = m::mock('Zend\Crypt\Symmetric\Mcrypt');
        $crypt->shouldReceive('encrypt')->andReturn(self::VALUE_ENCRYPTED);
        $crypt->shouldReceive('decrypt')->andReturn(self::VALUE);
        $crypt->shouldReceive('getSaltSize')->andReturn(0);
        $crypt->shouldReceive('setSalt')->andReturnSelf();

        $this->transformer = new ZendCryptSymmetricTransformer($crypt);
    }

    public function testTransform()
    {
        $this->assertEquals(self::VALUE_ENCRYPTED, $this->transformer->transform(self::VALUE));
    }

    public function testReverseTransform()
    {
        $this->assertEquals(self::VALUE, $this->transformer->reverseTransform(self::VALUE_ENCRYPTED));
    }

    public function testTransformReverseTransform()
    {
        $this->assertEquals(self::VALUE, $this->transformer->reverseTransform($this->transformer->transform(self::VALUE)));
    }
}