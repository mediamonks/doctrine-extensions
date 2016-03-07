<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Hash;

class ZendCryptHashTransformer implements TransformerInterface
{
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
        if(isset($options['algorithm'])) {
            $this->algorithm = $options['algorithm'];
        }
        if(isset($options['binary'])) {
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
        return Hash::compute($this->getAlgorithm(), $value, $this->getBinary());
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

