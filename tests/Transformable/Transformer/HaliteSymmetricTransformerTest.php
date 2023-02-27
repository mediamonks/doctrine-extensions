<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\HaliteSymmetricTransformer;
use ParagonIE\Halite\KeyFactory;
use PHPUnit\Framework\TestCase;
use Throwable;

class HaliteSymmetricTransformerTest extends TestCase
{
    const ENCRYPTION_KEY_PATH = __DIR__ . '/../Fixture/encryption.key';
    const VALUE_TO_ENCRYPT = 'foobar';

    protected function setUp(): void
    {
        if (!extension_loaded('sodium')) {
            $this->markTestSkipped('Libsodium not installed');
        }
        KeyFactory::save(
            KeyFactory::generateEncryptionKey(),
            self::ENCRYPTION_KEY_PATH
        );
    }

    protected function tearDown(): void
    {
        unlink(self::ENCRYPTION_KEY_PATH);
    }

    protected function getTransformerHex(): HaliteSymmetricTransformer
    {
        try {
            return new HaliteSymmetricTransformer(self::ENCRYPTION_KEY_PATH, ['binary' => false]);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function getTransformerBinary(): HaliteSymmetricTransformer
    {
        return new HaliteSymmetricTransformer(self::ENCRYPTION_KEY_PATH);
    }

    public function testBinaryDefaultEnabled(): void
    {
        $transformer = new HaliteSymmetricTransformer(self::ENCRYPTION_KEY_PATH);
        $this->assertTrue($transformer->getBinary());
    }

    public function testTransformHex(): void
    {
        try {
            $x = $this->getTransformerHex()->transform(self::VALUE_TO_ENCRYPT);
            $y = $this->getTransformerHex()->reverseTransform($x);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
        $this->assertEquals(self::VALUE_TO_ENCRYPT, $y);
    }

    public function testTransformNullValue(): void
    {
        try {
            $x = $this->getTransformerHex()->transform(null);
            $y = $this->getTransformerHex()->reverseTransform($x);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
        $this->assertEquals(null, $y);
    }

    public function testTransformBinary(): void
    {
        try {
            $x = $this->getTransformerBinary()->transform(self::VALUE_TO_ENCRYPT);
            $y = $this->getTransformerBinary()->reverseTransform($x);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
        $this->assertEquals(self::VALUE_TO_ENCRYPT, $y);
    }
}
