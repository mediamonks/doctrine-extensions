Example Symfony
===============

Configure services:

.. code-block:: yaml

    # app/config/parameters.yml
    parameters:
        defuse_encryption_key: def000008728e..........3672cb0efd
        zend_encryption_key: a_key_stronger_than_this_example
        hmac_key: a_key_stronger_than_this_example

    services:
        mediamonks.doctrine.transformable.transformer.defuse_encrypt_key:
            class: MediaMonks\Doctrine\Transformable\Transformer\DefuseCryptoEncryptKeyTransformer
            arguments: ["@defuse_encryption_key"]

        zend.crypt.symmetric_encrypter:
            class: Zend\Crypt\Symmetric\Mcrypt
            calls:
                - [setKey, [%zend_encryption_key%]]

        mediamonks.doctrine.transformable.transformer.zend_crypt_symmetric:
            class: MediaMonks\Doctrine\Transformable\Transformer\ZendCryptSymmetricTransformer
            arguments: ["@zend.crypt.symmetric_encrypter"]

        mediamonks.doctrine.transformable.transformer.zend_crypt_hash:
            class: MediaMonks\Doctrine\Transformable\Transformer\ZendCryptHashTransformer

        mediamonks.doctrine.transformable.transformer.zend_crypt_hmac:
            class: MediaMonks\Doctrine\Transformable\Transformer\ZendCryptHmacTransformer
            arguments: [%hmac_key%]

        mediamonks.doctrine.transformable.transformer_pool:
            class: MediaMonks\Doctrine\Transformable\Transformer\TransformerPool
            calls:
                - [set, ['defuse_encrypt_key', "@mediamonks.doctrine.transformable.transformer.defuse_encrypt_key"]]
                - [set, ['zend_encrypt', "@mediamonks.doctrine.transformable.transformer.zend_crypt_symmetric"]]
                - [set, ['zend_hash', "@mediamonks.doctrine.transformable.transformer.zend_crypt_hash"]]
                - [set, ['zend_hmac', "@mediamonks.doctrine.transformable.transformer.zend_crypt_hmac"]]

        doctrine.transformable.subscriber:
            class: MediaMonks\Doctrine\Transformable\TransformableSubscriber
            arguments: [@mediamonks.doctrine.transformable.transformer_pool]
            tags:
             - { name: doctrine.event_subscriber, priority: 100}

Configure entity:

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use MediaMonks\Doctrine\Mapping\Annotation as MediaMonks;

    /**
     * @ORM\Entity
     * @ORM\Table(name="examples")
     */
    class Example
    {
        /**
         * @ORM\Column(type="blob")
         * @MediaMonks\Transformable(name="defuse_encrypt_key")
         */
        protected $email;

        /**
         * @ORM\Column(type="blob")
         * @MediaMonks\Transformable(name="zend_hash")
         */
        protected $emailCanonical;
