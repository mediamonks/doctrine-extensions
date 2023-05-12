<?php

namespace MediaMonks\Doctrine\Transformable;

enum TransformerMethod: string
{
    case TRANSFORM = 'transform';
    case REVERSE_TRANSFORM = 'reverseTransform';
}
