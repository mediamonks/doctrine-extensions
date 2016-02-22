Example Symfony
===============

Configure services:

.. code-block:: yaml

    # app/config/parameters.yml
    parameters:
        encryption_key: a_key_stronger_than_this_example

    services:
        zend.crypt.symmetric_encrypter:
            class: Zend\Crypt\Symmetric\Mcrypt
            calls:
                - [setKey, [%encryption_key%]]

        mediamonks.doctrine.transformable.transformer.zend_crypt_symmetric:
            class: MediaMonks\Doctrine\Transformable\Transformer\ZendCryptSymmetricTransformer
            arguments: [@zend.crypt.symmetric_encrypter]

        mediamonks.doctrine.transformable.transformer_pool:
            class: MediaMonks\Doctrine\Transformable\Transformer\TransformerPool
            calls:
                - [set, ['encrypt_symmetric', @mediamonks.doctrine.transformable.transformer.zend_crypt_symmetric]]

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
         * @ORM\Column(type="string")
         * @MediaMonks\Transformable(name="encrypt_symmetric")
         */
        protected $fieldToEncrypt;