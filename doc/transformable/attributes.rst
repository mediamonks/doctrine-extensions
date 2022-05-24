Annotations
===========

.. code-block:: php

    <?php

    namespace Acme\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use MediaMonks\Doctrine\Mapping as MediaMonks;

    #[ORM\Entity()]
    class Foo
    {
        #[ORM\Column(type: 'text', nullable: true)]
        #[MediaMonks\Transformable(name: '<transformer_name>')]
        protected $bar;
