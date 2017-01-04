N86io/Hook documentation
========================

Manage configuration using arrays with validating keys and values.

Install
=======

For using this packages, please read `composer documentation
<https://getcomposer.org/doc>`_ how to use composer and packages for it.

Package name for this hook package is ``n86io/array-conf``.

Example
=======

For using array-conf, first you need a configuration definition, which define the configuration keys and the types for
it.

Here at first a complete definition-example:

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

A configuration for this template may be look like the following:

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

And now create instance of class ``\N86io\ArrayConf\Configuration``:

.. code-block:: php

    $configuration = new Configuration(
        $confDefinition,
        ConfigurationInterface::KEY_FLEXIBLE,
        ConfigurationInterface::TYPE_CAST
    );
    $configuration->add($conf);

You can add some further configuration arrays. The arrays will be merged. Second configuration will be overwrite first.

At last, just get the valid and merged configuration:

.. code-block:: php

    $configuration->get();

Types
=====

Base-Types (bool, int, float and string)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The base types are typical types how there are also in php.

Wildcard-Type (*)
^^^^^^^^^^^^^^^^^

This type means, that in the configuration no definite type are necessary.

Conf-Type (conf)
^^^^^^^^^^^^^^^^

In example above under 'index5' showed, there is sub-configuration for configuration key.

List-Type (list)
^^^^^^^^^^^^^^^^

Similar to 'conf'-type, but the configuration entries will be repeat. Each entry should have a key for it. Every
configuration-entry should have same structure.

If entry-keys are numeric, the entries will not be merged if same keys available. If on of the keys are not numeric,
the entries with same key will be merged.

Flexible or strict key handling
===============================

At creating instance of \N86io\ArrayConf\Configuration you have choice between flexible or strict key handling:

.. code-block:: php

    $configuration = new Configuration(
        $confDefinition,
        ConfigurationInterface::KEY_FLEXIBLE, <-- or ConfigurationInterface::KEY_STRICT
        ConfigurationInterface::TYPE_CAST
    );

This means, if you decided for ``ConfigurationInterface::KEY_STRICT``, in the configuration isn't allowed configure a
key, who are not defined in configuration-definition. With ``ConfigurationInterface::KEY_FLEXIBLE`` however it doesn't
matter, if the key is defined. If key is not defined, the type is either '*' or 'conf'. If value is an array, the type
will be 'conf', otherwise '*'.

Strict type or cast
===================

For type there is also a choice between strict or cast:

.. code-block:: php

    $configuration = new Configuration(
        $confDefinition,
        ConfigurationInterface::KEY_FLEXIBLE,
        ConfigurationInterface::TYPE_CAST <-- or ConfigurationInterface::TYPE_STRICT
    );

With ``ConfigurationInterface::TYPE_CAST`` the value will be cast to defined type, but only the type in definition is
a base-type, which are described above. With ``ConfigurationInterface::TYPE_STRICT`` differences between definition and
value type are not allowed.

API Documentation
=================

Coming soon...
