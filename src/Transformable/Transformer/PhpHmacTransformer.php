<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class PhpHmacTransformer extends AbstractHmacTransformer
{
    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return \hash_hmac($this->getAlgorithm(), $value, $this->getKey(), $this->getBinary());
    }
}
