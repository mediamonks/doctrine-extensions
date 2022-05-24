<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

interface TransformerInterface
{
    public function transform(?string $value): mixed;

    public function reverseTransform(?string $value): mixed;
}
