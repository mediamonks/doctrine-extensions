<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Hash;

class ZendCryptHashTransformerTest extends \PHPUnit_Framework_TestCase
{
    const ALGORITHM = 'sha256';

    /**
     * @var NoopTransformer
     */
    protected $transformer;

    protected function setUp()
    {
        $this->transformer = new ZendCryptHashTransformer(['algorithm' => self::ALGORITHM, 'binary' => false]);
    }

    public function testTransform()
    {
        $this->assertEquals(Hash::compute(self::ALGORITHM, ''), $this->transformer->transform(''));
    }

    public function testReverseTransform()
    {
        $this->assertEquals('', $this->transformer->reverseTransform(''));
    }
}