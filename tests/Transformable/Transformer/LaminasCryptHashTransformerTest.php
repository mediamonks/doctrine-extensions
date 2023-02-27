<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use Laminas\Crypt\Hash;
use MediaMonks\Doctrine\Transformable\Transformer\LaminasCryptHashTransformer;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

class LaminasCryptHashTransformerTest extends TestCase
{
    const ALGORITHM = 'sha256';
    const ALGORITHM_ALTERNATIVE = 'sha1';
    const VALUE_HEX = 'foobar';

    protected TransformerInterface $transformer;

    protected function setUp(): void
    {
        $this->transformer = new LaminasCryptHashTransformer(['algorithm' => self::ALGORITHM, 'binary' => false]);
    }

    public function testChangeAlgorithm(): void
    {
        $transformer = new LaminasCryptHashTransformer(['algorithm' => self::ALGORITHM_ALTERNATIVE]);
        $this->assertEquals(self::ALGORITHM_ALTERNATIVE, $transformer->getAlgorithm());
    }

    public function testBinaryDefaultEnabled(): void
    {
        $transformer = new LaminasCryptHashTransformer();
        $this->assertTrue($transformer->getBinary());
    }

    public function testDisableBinary(): void
    {
        $transformer = new LaminasCryptHashTransformer(['binary' => false]);
        $this->assertFalse($transformer->getBinary());
    }

    public function testTransformHex(): void
    {
        $this->assertEquals(Hash::compute($this->transformer->getAlgorithm(), self::VALUE_HEX, $this->transformer->getBinary()), $this->transformer->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex(): void
    {
        $this->assertEquals(self::VALUE_HEX, $this->transformer->reverseTransform(self::VALUE_HEX));
    }
}
