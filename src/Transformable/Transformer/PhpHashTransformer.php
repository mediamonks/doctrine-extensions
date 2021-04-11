<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class PhpHashTransformer extends AbstractHashTransformer
{
    /**
     * @param string $value
     * @return string | bool
     */
    public function transform($value)
    {
        return \hash($this->getAlgorithm(), $value, $this->getBinary());
    }
}
