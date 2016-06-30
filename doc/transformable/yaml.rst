Yaml
====

.. code-block:: php

    App\CoreBundle\Entity\User:
      type: entity
      table: users
      id:
        id:
          type: integer
          generator:
            strategy: AUTO
      fields:
        email:
          type: string
          nullable: true
          mediamonks:
              transformable:
                  name: encrypt
        emailCanonical:
          type: string
          nullable: true
          unique: true
          mediamonks:
              transformable:
                  name: hash