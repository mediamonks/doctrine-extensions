<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class PhpHashTransformer extends AbstractHashTransformer
{
    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return \hash($this->getAlgorithm(), $value, $this->getBinary());
    }
}
