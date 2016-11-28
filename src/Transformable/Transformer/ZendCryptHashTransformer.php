<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\Traits\GetValue;
use Zend\Crypt\Hash;

class ZendCryptHashTransformer implements TransformerInterface
{
    use GetValue;

    /**
     * @var string
     */
    protected $algorithm = 'sha256';

    /**
     * @var bool
     */
    protected $binary = true;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    protected function setOptions(array $options)
    {
        if(array_key_exists('algorithm', $options)) {
            $this->algorithm = $options['algorithm'];
        }
        if(array_key_exists('binary', $options)) {
            $this->binary = $options['binary'];
        }
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @return bool
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return Hash::compute($this->getAlgorithm(), $this->getValue($value), $this->getBinary());
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}

