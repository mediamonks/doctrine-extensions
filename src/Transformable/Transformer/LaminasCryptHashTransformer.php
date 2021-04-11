<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Laminas\Crypt\Hash;

class LaminasCryptHashTransformer extends AbstractHashTransformer
{
    /**
     * @param string $value
     * @return string
     */
    public function transform($value): string
    {
        return Hash::compute($this->getAlgorithm(), $value, $this->getBinary());
    }
}
