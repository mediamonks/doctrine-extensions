<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\LaminasCryptHashTransformer;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;
use Laminas\Crypt\Hash;

class LaminasCryptHashTransformerTest extends TestCase
{
    const ALGORITHM = 'sha256';
    const ALGORITHM_ALTERNATIVE = 'sha1';
    const VALUE_HEX = 'foobar';

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    protected function setUp(): void
    {
        $this->transformer = new LaminasCryptHashTransformer(['algorithm' => self::ALGORITHM, 'binary' => false]);
    }

    public function testChangeAlgorithm()
    {
        $transformer = new LaminasCryptHashTransformer(['algorithm' => self::ALGORITHM_ALTERNATIVE]);
        $this->assertEquals(self::ALGORITHM_ALTERNATIVE, $transformer->getAlgorithm());
    }

    public function testBinaryDefaultEnabled()
    {
        $transformer = new LaminasCryptHashTransformer();
        $this->assertTrue($transformer->getBinary());
    }

    public function testDisableBinary()
    {
        $transformer = new LaminasCryptHashTransformer(['binary' => false]);
        $this->assertFalse($transformer->getBinary());
    }

    public function testTransformHex()
    {
        $this->assertEquals(Hash::compute($this->transformer->getAlgorithm(), self::VALUE_HEX, $this->transformer->getBinary()), $this->transformer->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex()
    {
        $this->assertEquals(self::VALUE_HEX, $this->transformer->reverseTransform(self::VALUE_HEX));
    }
}
