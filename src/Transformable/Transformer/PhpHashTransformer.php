<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class PhpHashTransformer extends AbstractHashTransformer
{
    public function transform(?string $value): string|bool
    {
        if (empty($value)) {
            return false;
        }

        return hash($this->getAlgorithm(), $value, $this->getBinary());
    }
}
