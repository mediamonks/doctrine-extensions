<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Hash;

class ZendCryptHashTransformer extends AbstractHashTransformer
{
    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return Hash::compute($this->getAlgorithm(), $value, $this->getBinary());
    }
}
