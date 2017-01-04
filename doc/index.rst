N86io/ArrayConf documentation
=============================

Configuration management which handles the setup and validation of a configuration consisting of keys and values stored
in an array structure. It also handles the merging of two distinct configurations into one configuration.

Install
=======

For using this packages, please read `composer documentation <https://getcomposer.org/doc>`_ on how to use composer and
packages for it.

Package name for this array-conf package is ``n86io/array-conf``.

Example
=======

For using array-conf, first you need a configuration definition which defines the configuration keys and the types for
it.

First, here is a complete definition-example:

.. code-block:: php

    $confDefinition = [
        'index1' => [
            'type' => 'string'
        ],
        'index2' => [
            'type'    => 'int',
            'default' => 246
        ],
        'index3' => [
            'type' => 'float'
        ],
        'index4' => [
            'type'       => 'list',
            'definition' => [
                'index4_1' => [
                    'type' => 'int'
                ]
            ]
        ],
        'index5' => [
            'type'       => 'conf',
            'definition' => [
                'index5_1' => [
                    'type' => 'bool'
                ],
                'index5_2' => [
                    'type' => '*'
                ]
            ]
        ]
    ];

A configuration for this template may look like the following:

.. code-block:: php

    $conf = [
        'index1' => 'content1',
        'index2' => 123,
        'index3' => 1.23,
        'index4' => [
            'key1' => [
                'index4_1' => 123
            ],
            'key2' => [
                'index4_1' => 456
            ]
        ],
        'index5' => [
            'index5_1' => true,
            'index5_2' => 'something'
        ]
    ];

And now we create instance of class ``\N86io\ArrayConf\Configuration``:

.. code-block:: php

    $configuration = new Configuration(
        $confDefinition,
        ConfigurationInterface::KEY_FLEXIBLE,
        ConfigurationInterface::TYPE_CAST
    );
    $configuration->add($conf);

You can add further configuration arrays. The arrays will be merged. The second configuration will overwrite the
first.

At last, just get the valid and merged configuration:

.. code-block:: php

    $configuration->get();

Types
=====

Base-Types (bool, int, float and string)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The base types are the primary types used by PHP. Only those four types are allowed.

Wildcard-Type (*)
^^^^^^^^^^^^^^^^^

Using a wildcard will give you the option to use whatever type you want to use in the actual configuration except the
array type which needs to be declared.

Conf-Type (conf)
^^^^^^^^^^^^^^^^

As shown in the example above in 'index5', there is sub-configuration for configuration key.

List-Type (list)
^^^^^^^^^^^^^^^^

Similar to 'conf'-type, but the configuration entries will be repeated. Each entry should have a key for it. Every
configuration-entry should have same structure.

If the entry-indexes are numeric, the entry-values will not be merged if the same indexes are available in both
configurations. If one of the indexes is not numeric the entry-values with the same key will be merged.

Flexible or strict key handling
===============================

During creation of an instance of \N86io\ArrayConf\Configuration you have a choice between
``ConfigurationInterface::KEY_FLEXIBLE`` or ``ConfigurationInterface::KEY_STRICT`` key handling:

.. code-block:: php

    $configuration = new Configuration(
        $confDefinition,
        ConfigurationInterface::KEY_FLEXIBLE, <-- or ConfigurationInterface::KEY_STRICT
        ConfigurationInterface::TYPE_CAST
    );

This means if you decide to use ``ConfigurationInterface::KEY_STRICT``, in the configuration it isn't allowed to
configure a key who is not specified in the configuration-definition. With ``ConfigurationInterface::KEY_FLEXIBLE``
however it doesn't matter if the key is specified in the configuration-definition. If key is not defined, the type is
either '*' or 'conf'. If the value is an array the type will be 'conf', otherwise '*'.

Strict type or cast
===================

While deciding on a type there is a choice between the two options ``ConfigurationInterface::TYPE_CAST`` and
``ConfigurationInterface::TYPE_STRICT``:

.. code-block:: php

    $configuration = new Configuration(
        $confDefinition,
        ConfigurationInterface::KEY_FLEXIBLE,
        ConfigurationInterface::TYPE_CAST <-- or ConfigurationInterface::TYPE_STRICT
    );

When choosing ``ConfigurationInterface::TYPE_CAST`` the value will be cast to the defined type in the
configuration-definition, but only if the type is a base-type as is described above. With
``ConfigurationInterface::TYPE_STRICT`` differences between the defined type specified in the
configuration-definition and the actual type used are not allowed.

API Documentation
=================

Coming soon...
