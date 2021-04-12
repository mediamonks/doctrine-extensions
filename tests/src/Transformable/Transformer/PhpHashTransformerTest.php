<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use PHPUnit\Framework\TestCase;

class PhpHashTransformerTest extends TestCase
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
        $this->transformer = new PhpHashTransformer(['algorithm' => self::ALGORITHM, 'binary' => false]);
    }

    public function testChangeAlgorithm()
    {
        $transformer = new PhpHashTransformer(['algorithm' => self::ALGORITHM_ALTERNATIVE]);
        $this->assertEquals(self::ALGORITHM_ALTERNATIVE, $transformer->getAlgorithm());
    }

    public function testBinaryDefaultEnabled()
    {
        $transformer = new PhpHashTransformer();
        $this->assertTrue($transformer->getBinary());
    }

    public function testDisableBinary()
    {
        $transformer = new PhpHashTransformer(['binary' => false]);
        $this->assertFalse($transformer->getBinary());
    }

    public function testTransformHex()
    {
        $this->assertEquals(\hash(self::ALGORITHM, self::VALUE_HEX), $this->transformer->transform(self::VALUE_HEX));
    }

    public function testReverseTransformHex()
    {
        $this->assertEquals(self::VALUE_HEX, $this->transformer->reverseTransform(self::VALUE_HEX));
    }
}
