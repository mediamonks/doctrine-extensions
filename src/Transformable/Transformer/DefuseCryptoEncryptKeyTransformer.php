<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class DefuseCryptoEncryptKeyTransformer implements TransformerInterface
{
    private bool $binary = true;

    public function __construct(private string $encryptionKey, array $options = [])
    {
        $this->setOptions($options);
    }

    protected function setOptions(array $options)
    {
        if (array_key_exists('binary', $options)) {
            $this->binary = $options['binary'];
        }
    }

    /**
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function getKey(): Key
    {
        return Key::loadFromAsciiSafeString($this->encryptionKey);
    }

    public function getBinary(): bool
    {
        return $this->binary;
    }

    /**
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function transform(?string $value): string
    {
        if (empty($value)) {
            return false;
        }

        return Crypto::encrypt($value, $this->getKey(), $this->getBinary());
    }

    /**
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function reverseTransform(?string $value): string|null
    {
        if (empty($value)) {
            return null;
        }

        return Crypto::decrypt($value, $this->getKey(), $this->getBinary());
    }

}
