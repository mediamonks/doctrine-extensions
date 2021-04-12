<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use MediaMonks\Doctrine\Exception\InvalidArgumentException;

class TransformerPool implements \ArrayAccess
{
    /**
     * @var array
     */
    private $transformers = [];

    /**
     * @param $name
     * @return TransformerInterface|null
     * @throws \Exception
     */
    public function get($name): ?TransformerInterface
    {
        return $this->offsetGet($name);
    }

    /**
     * @param $name
     * @param TransformerInterface $transformer
     * @return $this
     * @throws \Exception
     */
    public function set($name, TransformerInterface $transformer): TransformerPool
    {
        return $this->offsetSet($name, $transformer);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name): bool
    {
        return array_key_exists($name, $this->transformers);
    }

    /**
     * @param mixed $name
     * @return TransformerInterface|null
     * @throws \Exception
     */
    public function offsetGet($name): ?TransformerInterface
    {
        if(!$this->offsetExists($name)) {
            throw new InvalidArgumentException(sprintf('Transformer with name "%s" is not set', $name));
        }
        return $this->transformers[$name];
    }

    /**
     * @param mixed $key
     * @param mixed $transformer
     * @throws \Exception
     * @return $this
     */
    public function offsetSet($key, $transformer): TransformerPool
    {
        $this->transformers[$key] = $transformer;
        return $this;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function offsetUnset($name): TransformerPool
    {
        if($this->offsetExists($name)) {
            unset($this->transformers[$name]);
        }
        return $this;
    }
}
