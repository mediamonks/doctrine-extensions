<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class ReverseTransformer extends AbstractTransformer
{
    /**
     * @param string $value
     * @return mixed
     */
    public function transform($value)
    {
        return strrev($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return strrev($value);
    }
}