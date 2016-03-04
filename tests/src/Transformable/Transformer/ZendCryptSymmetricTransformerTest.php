<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Mockery as m;

class ZendCryptSymmetricTransformerTest extends \PHPUnit_Framework_TestCase
{
    const VALUE_HEX = 'foobar';
    const VALUE_HEX_ENCRYPTED = 'foobar_encrypted';

    const VALUE_BINARY = 'foobar_binary';
    const VALUE_BINARY_ENCRYPTED = 'foobar_binary_encrypted';

    protected function getMockCrypt($encrypted, $decrypted)
    {
        $crypt = m::mock('Zend\Crypt\Symmetric\Mcrypt');
        $crypt->shouldReceive('getSaltSize')->andReturn(0);
        $crypt->shouldReceive('setSalt')->andReturnSelf();
        $crypt->shouldReceive('encrypt')->andReturn($encrypted);
        $crypt->shouldReceive('decrypt')->andReturn($decrypted);
        return $crypt;
    }

    protected function getTransformerHex()
    {
        return new ZendCryptSymmetricTransformer($this->getMockCrypt(self::VALUE_HEX_ENCRYPTED, self::VALUE_HEX), ['binary' => false]);
    }

    protected function getTransformerBinary()
    {
        return new ZendCryptSymmetricTransformer($this->getMockCrypt(self::VALUE_BINARY_ENCRYPTED, self::VALUE_BINARY));
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
        $this->assertEquals(bin2hex(self::VALUE_HEX_ENCRYPTED), $this->getTransformerHex()->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex()
    {
        $this->assertEquals(self::VALUE_HEX, $this->getTransformerHex()->reverseTransform(bin2hex(self::VALUE_HEX_ENCRYPTED)));
    }

    public function testTransformReverseTransformHex()
    {
        $transformer = $this->getTransformerHex();
        $this->assertEquals(self::VALUE_HEX, $transformer->reverseTransform($transformer->transform(self::VALUE_HEX)));
    }

    public function testTransformBinary()
    {
        $this->assertEquals(self::VALUE_BINARY_ENCRYPTED, $this->getTransformerBinary()->transform(self::VALUE_BINARY));
    }

    public function testReverseTransformBinary()
    {
        $this->assertEquals(self::VALUE_BINARY, $this->getTransformerBinary()->reverseTransform(self::VALUE_BINARY_ENCRYPTED));
    }

    public function testTransformReverseTransformBinary()
    {
        $this->assertEquals(self::VALUE_BINARY, $this->getTransformerBinary()->reverseTransform($this->getTransformerBinary()->transform(self::VALUE_BINARY)));
    }
}