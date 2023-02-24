<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use ArrayAccess;
use Exception;
use MediaMonks\Doctrine\Exception\InvalidArgumentException;

class TransformerPool implements ArrayAccess
{
    private array $transformers = [];

    /**
     * @throws Exception
     */
    public function get(string $name): ?TransformerInterface
    {
        return $this->offsetGet($name);
    }

    /**
     * @throws Exception
     */
    public function set(string $name, TransformerInterface $transformer): TransformerPool
    {
        $this->offsetSet($name, $transformer);
        return $this;
    }

    public function offsetExists(mixed $name): bool
    {
        return array_key_exists($name, $this->transformers);
    }

    /**
     * @throws Exception
     */
    public function offsetGet(mixed $name): ?TransformerInterface
    {
        if (!$this->offsetExists($name)) {
            throw new InvalidArgumentException(sprintf('Transformer with name "%s" is not set', $name));
        }

        return $this->transformers[$name];
    }

    /**
     * @throws Exception
     */
    public function offsetSet(mixed $key, mixed $transformer): void
    {
        $this->transformers[$key] = $transformer;
    }

    public function offsetUnset(mixed $name): void
    {
        if ($this->offsetExists($name)) {
            unset($this->transformers[$name]);
        }
    }
}
