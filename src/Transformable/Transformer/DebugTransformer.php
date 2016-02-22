<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class DebugTransformer extends AbstractTransformer
{
    /**
     * @param string $value
     * @return mixed
     */
    public function transform($value)
    {
        return $value;
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
