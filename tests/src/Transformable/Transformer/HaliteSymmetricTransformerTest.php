<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use ParagonIE\Halite\KeyFactory;

class HaliteSymmetricTransformerTest extends \PHPUnit_Framework_TestCase
{
    const ENCRYPTION_KEY_PATH = __DIR__ . '/../Fixture/encryption.key';
    const VALUE_TO_ENCRYPT = 'foobar';

    protected function setUp()
    {
        if (!extension_loaded('libsodium')) {
            $this->markTestSkipped('Libsodium not installed');
        }
        KeyFactory::save(
            KeyFactory::generateEncryptionKey(),
            self::ENCRYPTION_KEY_PATH
        );
    }

    protected function tearDown()
    {
        unlink(self::ENCRYPTION_KEY_PATH);
    }

    protected function getTransformerHex()
    {
        return new HaliteSymmetricTransformer(self::ENCRYPTION_KEY_PATH, ['binary' => false]);
    }

    protected function getTransformerBinary()
    {
        return new HaliteSymmetricTransformer(self::ENCRYPTION_KEY_PATH);
    }

    public function testBinaryDefaultEnabled()
    {
        $transformer = new HaliteSymmetricTransformer(self::ENCRYPTION_KEY_PATH);
        $this->assertTrue($transformer->getBinary());
    }

    public function testTransformHex()
    {
        $x = $this->getTransformerHex()->transform(self::VALUE_TO_ENCRYPT);
        $y = $this->getTransformerHex()->reverseTransform($x);

        $this->assertEquals(self::VALUE_TO_ENCRYPT, $y);
    }

    public function testTransformNullValue()
    {
        $x = $this->getTransformerHex()->transform(null);
        $y = $this->getTransformerHex()->reverseTransform($x);

        $this->assertEquals(null, $y);
    }

    public function testTransformBinary()
    {
        $x = $this->getTransformerBinary()->transform(self::VALUE_TO_ENCRYPT);
        $y = $this->getTransformerBinary()->reverseTransform($x);

        $this->assertEquals(self::VALUE_TO_ENCRYPT, $y);
    }
}
