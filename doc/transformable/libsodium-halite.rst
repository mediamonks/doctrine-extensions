Libsodium & Halite
===========

PHP 7+ comes with libsodium included as `sodium`
Halite (Halite is a high-level cryptography interface) https://github.com/paragonie/halite

When you configure the LibsodiumCryptHashTransformer you need to generate an index key:
You would use the hash transformer for blind indexes (searchable versions of encrypted fields).

High entropy or low entropy?
- If we have high-entropy input we can just use shared-key authentication (crypto_auth, it uses HMAC).
- If we have low-entropy input eg. SSN, phone numbers, emails. We must use a password hashing function (Argon2).
  Our second key becomes a salt.

source: https://www.youtube.com/watch?v=Q2xGy3AGGSo&t=475s

For the HaliteSymmetricTransformer you need to generate an encryption key:

.. code-block:: php

    <?php

    use ParagonIE\Halite\KeyFactory;

    $encKey = KeyFactory::generateEncryptionKey();
    KeyFactory::save($encKey, '/path/outside/webroot/encryption.key');