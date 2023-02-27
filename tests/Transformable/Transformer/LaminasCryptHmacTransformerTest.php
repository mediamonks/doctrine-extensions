<?php

namespace MediaMonks\Doctrine\Tests\Transformable\Transformer;

use Laminas\Crypt\Hmac;
use MediaMonks\Doctrine\Transformable\Transformer\LaminasCryptHmacTransformer;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

class LaminasCryptHmacTransformerTest extends TestCase
{
    const ALGORITHM = 'sha256';
    const KEY = '7922GS0S3LoF2T5anKX4zAx4ED3463dyXFK7s1bp';
    const BINARY = false;
    const VALUE = 'foobar';

    protected TransformerInterface $transformer;

    protected function setUp(): void
    {
        $this->transformer = new LaminasCryptHmacTransformer(self::KEY, [
            'algorithm' => self::ALGORITHM,
            'binary' => self::BINARY
        ]);
    }

    public function testTransform(): void
    {
        $this->assertEquals(
            Hmac::compute(self::KEY, self::ALGORITHM, self::VALUE, self::BINARY),
            $this->transformer->transform(self::VALUE)
        );
    }

    public function testReverseTransform(): void
    {
        $this->assertEquals(self::VALUE, $this->transformer->reverseTransform(self::VALUE));
    }
}
