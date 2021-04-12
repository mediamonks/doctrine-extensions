<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use PHPUnit\Framework\TestCase;

class DefuseCryptoEncryptKeyTransformerTest extends TestCase
{
    const KEY = 'def000008728ec119b871b3da49a98976a2538395ae3a1d9f6090baf40fd6531c8b70eb292b560f991e85629960568e323912c73c71be8879a009a3d9329383672cb0efd';

    const VALUE_HEX = 'foobar';
    const VALUE_HEX_ENCRYPTED = 'foobar_encrypted';

    const VALUE_BINARY = 'foobar_binary';
    const VALUE_BINARY_ENCRYPTED = 'foobar_binary_encrypted';

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    protected function setUp(): void
    {
        $this->transformer = new DefuseCryptoEncryptKeyTransformer(self::KEY);
    }

    protected function getTransformerHex()
    {
        $mock = \Mockery::mock('alias:Defuse\Crypto\Crypto');
        $mock->shouldReceive('encrypt')->andReturn(self::VALUE_HEX_ENCRYPTED);
        $mock->shouldReceive('decrypt')->andReturn(self::VALUE_HEX);
        return new DefuseCryptoEncryptKeyTransformer(self::KEY, ['binary' => false]);
    }

    protected function getTransformerBinary()
    {
        $mock = \Mockery::mock('alias:Defuse\Crypto\Crypto');
        $mock->shouldReceive('encrypt')->andReturn(self::VALUE_BINARY_ENCRYPTED);
        $mock->shouldReceive('decrypt')->andReturn(self::VALUE_BINARY);
        return new DefuseCryptoEncryptKeyTransformer(self::KEY);
    }

    protected function tearDown(): void
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testBinaryDefaultEnabled()
    {
        $transformer = new DefuseCryptoEncryptKeyTransformer(self::KEY);
        $this->assertTrue($transformer->getBinary());
    }

    public function testDisableBinary()
    {
        $transformer = new DefuseCryptoEncryptKeyTransformer(self::KEY, ['binary' => false]);
        $this->assertFalse($transformer->getBinary());
    }

    public function testTransformHex()
    {
        $this->assertEquals(self::VALUE_HEX_ENCRYPTED, $this->getTransformerHex()->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex()
    {
        $this->assertEquals(self::VALUE_HEX, $this->getTransformerHex()->reverseTransform(self::VALUE_HEX_ENCRYPTED));
    }

    public function testTransformBinary()
    {
        $this->assertEquals(self::VALUE_BINARY_ENCRYPTED, $this->getTransformerBinary()->transform(self::VALUE_BINARY));
    }

    public function testReverseTransformBinary()
    {
        $this->assertEquals(self::VALUE_BINARY, $this->getTransformerBinary()->reverseTransform(self::VALUE_BINARY_ENCRYPTED));
    }
}
