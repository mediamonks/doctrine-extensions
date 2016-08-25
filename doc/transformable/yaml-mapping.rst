Yaml Mapping
===========

.. code-block:: yaml

    Domain\Entities\Foo:
      type: entity
      table: foo_bars
      id:
        id:
          type: guid
      fields:
        email:
          type: string
          mediamonks:
            transformable:
              name: encrypt
        emailCanonical:
          type: string
          mediamonks:
            transformable:
              name: hash
