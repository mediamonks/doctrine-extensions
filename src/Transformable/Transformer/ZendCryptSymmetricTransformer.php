<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Symmetric\SymmetricInterface;
use Zend\Math\Rand;

class ZendCryptSymmetricTransformer implements TransformerInterface
{
    /**
     * @var SymmetricInterface
     */
    protected $crypt;

    /**
     * @var bool
     */
    protected $binary = true;

    /**
     * @var bool
     */
    protected $requireStrongRandomGenerator = true;

    /**
     * @param SymmetricInterface $crypt
     * @param array $options
     */
    public function __construct(SymmetricInterface $crypt, array $options = [])
    {
        $this->crypt = $crypt;
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    protected function setOptions(array $options)
    {
        if (array_key_exists('binary', $options)) {
            $this->binary = $options['binary'];
        }
        if (array_key_exists('requireStrongRandomGenerator', $options)) {
            $this->requireStrongRandomGenerator = $options['requireStrongRandomGenerator'];
        }
    }

    /**
     * @return bool
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * @return bool
     */
    public function getRequireStrongRandomGenerator()
    {
        return $this->requireStrongRandomGenerator;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        $this->updateSalt();
        $value = $this->crypt->encrypt($value);

        if (!$this->getBinary()) {
            $value = bin2hex($value);
        }

        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!$this->getBinary()) {
            $value = hex2bin($value);
        }

        return $this->crypt->decrypt($value);
    }

    /**
     *
     */
    protected function updateSalt()
    {
        $this->crypt->setSalt(Rand::getBytes($this->crypt->getSaltSize(), $this->getRequireStrongRandomGenerator()));
    }
}

