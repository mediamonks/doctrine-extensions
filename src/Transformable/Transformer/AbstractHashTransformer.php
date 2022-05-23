<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

abstract class AbstractHashTransformer implements TransformerInterface
{
    protected string $algorithm = 'sha256';

    protected bool $binary = true;

    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    protected function setOptions(array $options)
    {
        if (array_key_exists('algorithm', $options)) {
            $this->algorithm = $options['algorithm'];
        }

        if (array_key_exists('binary', $options)) {
            $this->binary = $options['binary'];
        }
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function getBinary(): bool
    {
        return $this->binary;
    }

    public abstract function transform(?string $value): string|bool;

    public function reverseTransform(?string $value): string|null
    {
        return $value;
    }
}
