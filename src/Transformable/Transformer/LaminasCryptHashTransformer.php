<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Laminas\Crypt\Hash;

class LaminasCryptHashTransformer extends AbstractHashTransformer
{
    public function transform(?string $value): string|bool
    {
        if (empty($value)) {
            return false;
        }

        return Hash::compute($this->getAlgorithm(), $value, $this->getBinary());
    }
}
