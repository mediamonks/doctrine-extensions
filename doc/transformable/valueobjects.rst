Value Objects
=============

Value Objects are an essential concept when implementing Domain Driven Design, but they can be used in any PHP project.

In order to properly transform an object it needs to implement the **__toString()** method,
else an **MediaMonks\Doctrine\Exception\InvalidArgumentException** will be thrown.

.. code-block:: php

    <?php

    namespace Acme\ValueObjects;

    class Email
    {
        private $value;

        public function __construct($value)
        {
            $this->value = $value;
        }

        public static function fromNative($value)
        {
            return new self($value);
        }

        public function toNative()
        {
            return $this->__toString();
        }

        public function __toString()
        {
            return $this->value;
        }
    }

    namespace Acme\Entities;

    class User
    {
        /**
         * @var Acme\ValueObjects\Email
         */
        private $email;
    }