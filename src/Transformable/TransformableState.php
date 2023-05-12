<?php

namespace MediaMonks\Doctrine\Transformable;

enum TransformableState: string
{
    case TRANSFORMED = 'transformed';
    case PLAIN = 'plain';
}
