<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use PHPUnit\Framework\TestCase;

class LaminasCryptSymmetricTransformerTest extends TestCase
{
    const KEY = 'def000008728ec119b871b3da49a98976a2538395ae3a1d9f6090baf40fd6531c8b70eb292b560f991e85629960568e323912c73c71be8879a009a3d9329383672cb0efd';
    const VALUE_HEX = 'foobar';
    const VALUE_BINARY = 'foobar_binary';

    protected function getTransformerHex(): LaminasCryptSymmetricTransformer
    {
        return new LaminasCryptSymmetricTransformer(self::KEY, ['binary' => false]);
    }

    protected function getTransformerBinary(): LaminasCryptSymmetricTransformer
    {
        return new LaminasCryptSymmetricTransformer(self::KEY);
    }

    public function testBinaryDefaultEnabled()
    {
        $this->assertTrue($this->getTransformerBinary()->getBinary());
    }

    public function testDisableBinary()
    {
        $this->assertFalse($this->getTransformerHex()->getBinary());
    }

    public function testTransformHex()
    {
        $encrypted = $this->getTransformerHex()->transform(self::VALUE_HEX);
        $this->assertEquals(self::VALUE_HEX, $this->getTransformerHex()->reverseTransform($encrypted));
    }

    public function testReverseTransformHex()
    {
        $encrypted = $this->getTransformerHex()->transform(self::VALUE_HEX);
        $this->assertEquals(self::VALUE_HEX, $this->getTransformerHex()->reverseTransform($encrypted));
    }

    public function testTransformReverseTransformHex()
    {
        $transformer = $this->getTransformerHex();
        $this->assertEquals(self::VALUE_HEX, $transformer->reverseTransform($transformer->transform(self::VALUE_HEX)));
    }

    public function testTransformBinary()
    {
        $encrypted = $this->getTransformerBinary()->transform(self::VALUE_BINARY);
        $this->assertEquals(hex2bin(bin2hex(self::VALUE_BINARY)), $this->getTransformerBinary()->reverseTransform($encrypted));
    }

    public function testReverseTransformBinary()
    {
        $encrypted = $this->getTransformerBinary()->transform(self::VALUE_BINARY);
        $this->assertEquals(self::VALUE_BINARY, $this->getTransformerBinary()->reverseTransform($encrypted));
    }

    public function testTransformReverseTransformBinary()
    {
        $this->assertEquals(self::VALUE_BINARY, $this->getTransformerBinary()->reverseTransform($this->getTransformerBinary()->transform(self::VALUE_BINARY)));
    }
}
