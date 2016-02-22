<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class TransformerPool implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $transformers = [];

    /**
     * @param $key
     * @return TransformerInterface|null
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param $key
     * @param TransformerInterface $value
     * @return $this
     */
    public function set($key, TransformerInterface $value)
    {
        return $this->offsetSet($key, $value);
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->transformers);
    }

    /**
     * @param mixed $key
     * @return TransformerInterface|null
     * @throws \Exception
     */
    public function offsetGet($key)
    {
        if(!$this->offsetExists($key)) {
            throw new \Exception(sprintf('Transformer with name "%s" is not set', $key));
        }
        return $this->transformers[$key];
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @throws \Exception
     * @return $this
     */
    public function offsetSet($key, $value)
    {
        if(!$value instanceof TransformerInterface) {
            throw new \Exception('Value should be an instnce of TransformerInterface');
        }
        $this->transformers[$key] = $value;
        return $this;
    }

    /**
     * @param mixed $key
     * @return $this
     */
    public function offsetUnset($key)
    {
        if($this->offsetExists($key)) {
            unset($this->transformers[$key]);
        }
        return $this;
    }
}
