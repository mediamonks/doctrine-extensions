<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Hash;

class ZendCryptHashTransformerTest extends \PHPUnit_Framework_TestCase
{
    const ALGORITHM = 'sha256';
    const ALGORITHM_ALTERNATIVE = 'sha1';
    const VALUE_HEX = 'foobar';

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    protected function setUp()
    {
        $this->transformer = new ZendCryptHashTransformer(['algorithm' => self::ALGORITHM, 'binary' => false]);
    }

    public function testChangeAlgorithm()
    {
        $transformer = new ZendCryptHashTransformer(['algorithm' => self::ALGORITHM_ALTERNATIVE]);
        $this->assertEquals(self::ALGORITHM_ALTERNATIVE, $transformer->getAlgorithm());
    }

    public function testBinaryDefaultEnabled()
    {
        $transformer = new ZendCryptHashTransformer();
        $this->assertTrue($transformer->getBinary());
    }

    public function testDisableBinary()
    {
        $transformer = new ZendCryptHashTransformer(['binary' => false]);
        $this->assertFalse($transformer->getBinary());
    }

    public function testTransformHex()
    {
        $this->assertEquals(Hash::compute(self::ALGORITHM, self::VALUE_HEX), $this->transformer->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex()
    {
        $this->assertEquals(self::VALUE_HEX, $this->transformer->reverseTransform(self::VALUE_HEX));
    }
}
