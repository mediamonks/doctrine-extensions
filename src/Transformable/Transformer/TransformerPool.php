<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use MediaMonks\Doctrine\InvalidArgumentException;

class TransformerPool implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $transformers = [];

    /**
     * @param $name
     * @return TransformerInterface|null
     */
    public function get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param $name
     * @param TransformerInterface $transformer
     * @return $this
     */
    public function set($name, TransformerInterface $transformer)
    {
        return $this->offsetSet($name, $transformer);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->transformers);
    }

    /**
     * @param mixed $name
     * @return TransformerInterface|null
     * @throws \Exception
     */
    public function offsetGet($name)
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
    public function offsetSet($key, $transformer)
    {
        if(!$transformer instanceof TransformerInterface) {
            throw new InvalidArgumentException('Transformer should be an instance of TransformerInterface');
        }
        $this->transformers[$key] = $transformer;
        return $this;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function offsetUnset($name)
    {
        if($this->offsetExists($name)) {
            unset($this->transformers[$name]);
        }
        return $this;
    }
}
