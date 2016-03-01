Annotations
===========

.. code-block:: php

    <?php

    namespace Acme\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use MediaMonks\Doctrine\Mapping\Annotation as MediaMonks;

    /**
     * @ORM\Entity
     */
    class Foo
    {
        /**
         * @ORM\Column(type="blob")
         * @MediaMonks\Transformable(name="<transformer_name>")
         */
        protected $bar;