<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use ParagonIE\Halite\Halite;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;

class HaliteSymmetricTransformer implements TransformerInterface
{
    const HALITE_LEGACY_VERSION = '1.0.0';

    /**
     * @var bool
     */
    private $binary = true;

    /**
     * @var EncryptionKey
     */
    private $encryptionKey = null;

    /**
     * @param $encryptionKey
     * @param array $options
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \SodiumException
     */
    public function __construct($encryptionKey, array $options = [])
    {
        $this->encryptionKey = KeyFactory::loadEncryptionKey($encryptionKey);

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
    }

    /**
     * @return bool
     */
    public function getBinary(): bool
    {
        return $this->binary;
    }

    /**
     * @param string $value
     * @return string | null
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     * @throws \SodiumException
     */
    public function transform($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($this->binary) {
            $value = \Sodium\bin2hex($value);
        }

        if (Halite::VERSION > self::HALITE_LEGACY_VERSION) {
            $value = new HiddenString($value);
        }

        return Crypto::encrypt($value, $this->encryptionKey);
    }

    /**
     * @param string $value
     * @return string|null
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSignature
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     * @throws \SodiumException
     */
    public function reverseTransform($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $decryptedValue = Crypto::decrypt($value, $this->encryptionKey);

        if (Halite::VERSION > self::HALITE_LEGACY_VERSION) {
            $decryptedValue = $decryptedValue->getString();
        }

        if (!$this->binary) {
            return $decryptedValue;
        }

        return \Sodium\hex2bin($decryptedValue);
    }
}

