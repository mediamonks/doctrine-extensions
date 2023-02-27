<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\PhpHashTransformer;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;
use function hash;

class PhpHashTransformerTest extends TestCase
{
    const ALGORITHM = 'sha256';
    const ALGORITHM_ALTERNATIVE = 'sha1';
    const VALUE_HEX = 'foobar';

    protected TransformerInterface $transformer;

    protected function setUp(): void
    {
        $this->transformer = new PhpHashTransformer(['algorithm' => self::ALGORITHM, 'binary' => false]);
    }

    public function testChangeAlgorithm(): void
    {
        $transformer = new PhpHashTransformer(['algorithm' => self::ALGORITHM_ALTERNATIVE]);
        $this->assertEquals(self::ALGORITHM_ALTERNATIVE, $transformer->getAlgorithm());
    }

    public function testBinaryDefaultEnabled(): void
    {
        $transformer = new PhpHashTransformer();
        $this->assertTrue($transformer->getBinary());
    }

    public function testDisableBinary(): void
    {
        $transformer = new PhpHashTransformer(['binary' => false]);
        $this->assertFalse($transformer->getBinary());
    }

    public function testTransformHex(): void
    {
        $this->assertEquals(hash(self::ALGORITHM, self::VALUE_HEX), $this->transformer->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex(): void
    {
        $this->assertEquals(self::VALUE_HEX, $this->transformer->reverseTransform(self::VALUE_HEX));
    }
}
