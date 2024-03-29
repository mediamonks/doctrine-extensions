<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Laminas\Crypt\Hmac;

class LaminasCryptHmacTransformer extends AbstractHmacTransformer
{
    /**
     * @param string $value
     * @return string | null
     */
    public function transform($value): ?string
    {
        return Hmac::compute($this->getKey(), $this->algorithm, $value, $this->getBinary());
    }
}
