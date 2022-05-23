<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Laminas\Crypt\Hmac;

class LaminasCryptHmacTransformer extends AbstractHmacTransformer
{
    public function transform(?string $value): string|bool
    {
        if (empty($value)) {
            return false;
        }

        return Hmac::compute($this->getKey(), $this->algorithm, $value, $this->getBinary());
    }
}
