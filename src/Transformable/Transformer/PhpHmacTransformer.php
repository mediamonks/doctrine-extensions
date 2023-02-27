<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class PhpHmacTransformer extends AbstractHmacTransformer
{
    public function transform(?string $value): string|bool
    {
        if (empty($value)) {
            return false;
        }

        return hash_hmac($this->getAlgorithm(), $value, $this->getKey(), $this->getBinary());
    }
}
