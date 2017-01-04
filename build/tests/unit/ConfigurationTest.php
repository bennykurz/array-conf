<?php declare(strict_types = 1);
/**
 * This file is part of N86io/ArrayConf.
 *
 * N86io/ArrayConf is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * N86io/ArrayConf is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with N86io/ArrayConf or see <http://www.gnu.org/licenses/>.
 */

namespace N86io\ArrayConf\Tests\Unit;

use N86io\ArrayConf\Configuration;
use N86io\ArrayConf\ConfigurationInterface;
use N86io\ArrayConf\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  1.0.0
 */
class ConfigurationTest extends TestCase
{
    public function testKeyFlexible()
    {
        $confDefinition = [
            'index1' => [
                'type' => 'string'
            ],
            'index2' => [
                'type'       => 'conf',
                'definition' => [
                    'index2_1' => [
                        'type' => 'bool'
                    ]
                ]
            ],
            'index3' => [
                'type'       => 'list',
                'definition' => [
                    'index3_1' => [
                        'type' => 'int'
                    ]
                ]
            ]
        ];
        $conf = [
            'index1' => 'test',
            'index2' => [
                'index2_1' => true,
                'index2_2' => false
            ],
            'index3' => [
                'abc' => [
                    'index3_1' => 1,
                    'index3_2' => 2
                ],
                'def' => [
                    'index3_3' => 3
                ]
            ],
            'index4' => 3,
            'index5' => [
                'index5_1' => 'test'
            ]
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_CAST
        );
        $configuration->add($conf);

        $this->assertEquals($conf, $configuration->get());


        $conf2 = [
            'index2' => [
                'index2_2' => true,
                'index2_3' => false
            ],
            'index3' => [
                'def' => [
                    'index3_4' => 'test'
                ]
            ],
            'index6' => 'test'
        ];
        $expected2 = $conf;
        $expected2['index2']['index2_2'] = true;
        $expected2['index2']['index2_3'] = false;
        $expected2['index3']['def']['index3_4'] = 'test';
        $expected2['index6'] = 'test';

        $configuration->add($conf2);

        $this->assertEquals($expected2, $configuration->get());
    }

    public function testTypeCast()
    {
        $confDefinition = [
            'index1' => [
                'type' => 'string'
            ],
            'index2' => [
                'type' => 'int'
            ],
            'index3' => [
                'type' => 'float'
            ]
        ];
        $conf = [
            'index1' => 1,
            'index2' => '2',
            'index3' => '3.4'
        ];
        $expected = [
            'index1' => '1',
            'index2' => 2,
            'index3' => 3.4
        ];

        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_CAST
        );
        $configuration->add($conf);

        $this->assertEquals($expected, $configuration->get());


        $conf2 = [
            'index2' => '5',
            'index3' => 6.7
        ];
        $expected2 = $expected;
        $expected2['index2'] = 5;
        $expected2['index3'] = 6.7;

        $configuration->add($conf2);
        $this->assertEquals($expected2, $configuration->get());
    }

    public function testDefault()
    {
        $confDefinition = [
            'index1' => [
                'type'    => 'string',
                'default' => 'defaulttest'
            ],
            'index2' => [
                'type'    => 'int',
                'default' => 123
            ]
        ];
        $conf = [
            'index1' => 'test'
        ];
        $expected = $conf;
        $expected['index2'] = 123;

        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_CAST
        );
        $configuration->add($conf);

        $this->assertEquals($expected, $configuration->get());
    }

    public function testConfListAssociativeAndNumeric()
    {
        $confDefinition = [
            'index1' => [
                'type'       => 'list',
                'definition' => [
                    'index1_1' => [
                        'type' => 'bool'
                    ]
                ]
            ]
        ];
        $conf1 = [
            'index1' => [
                'abc' => [
                    'index1_1' => true
                ],
                'def' => [
                    'index1_1' => false
                ]
            ]
        ];
        $conf2 = [
            'index1' => [
                'def' => [
                    'index1_1' => true
                ],
                'ghi' => [
                    'index1_1' => false
                ]
            ]
        ];
        $expected = [
            'index1' => [
                'abc' => [
                    'index1_1' => true
                ],
                'def' => [
                    'index1_1' => true
                ],
                'ghi' => [
                    'index1_1' => false
                ]
            ]
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf1)->add($conf2);
        $this->assertEquals($expected, $configuration->get());


        $conf1 = [
            'index1' => [
                [
                    'index1_1' => true
                ],
                [
                    'index1_1' => false
                ]
            ]
        ];
        $conf2 = [
            'index1' => [
                [
                    'index1_1' => true
                ],
                [
                    'index1_1' => false
                ]
            ]
        ];
        $expected = [
            'index1' => [
                [
                    'index1_1' => true
                ],
                [
                    'index1_1' => false
                ],
                [
                    'index1_1' => true
                ],
                [
                    'index1_1' => false
                ]
            ]
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf1)->add($conf2);
        $this->assertEquals($expected, $configuration->get());
    }

    public function testTypeStrict()
    {
        $confDefinition = [
            'index1' => [
                'type' => 'int'
            ]
        ];
        $conf = [
            'index1' => 123456789,
            'index2' => 123456789
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
        $this->assertEquals($conf, $configuration->get());
    }

    public function testTypeStrictException1()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1483365239);
        $this->expectExceptionMessage('Invalid value for "* > index1". Expected type "int", got "float".');
        $confDefinition = [
            'index1' => [
                'type' => 'int'
            ]
        ];
        $conf = [
            'index1' => 12345678.9
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
    }

    public function testTypeStrictException2()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1483365239);
        $this->expectExceptionMessage('Invalid value for "* > index1". Expected type "conf", got "float".');
        $confDefinition = [
            'index1' => [
                'type'       => 'conf',
                'definition' => [
                    'index1_1' => 'string'
                ]
            ]
        ];
        $conf = [
            'index1' => 12345678.9
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
    }

    public function testKeyStrict()
    {
        $confDefinition = [
            'index1' => [
                'type'       => 'conf',
                'definition' => [
                    'index1_1' => [
                        'type' => 'bool'
                    ]
                ]
            ],
            'index2' => [
                'type'       => 'list',
                'definition' => [
                    'index2_1' => [
                        'type' => 'int'
                    ],
                    'index2_2' => [
                        'type' => 'float'
                    ]
                ]
            ],
            'index3' => [
                'type' => 'bool'
            ]
        ];
        $conf = [
            'index1' => [
                'index1_1' => true
            ],
            'index2' => [
                'abc' => [
                    'index2_1' => 123,
                    'index2_2' => 1.23
                ],
                'def' => [
                    'index2_1' => 456,
                    'index2_2' => 4.56
                ]
            ],
            'index3' => 1
        ];
        $expected = $conf;
        $expected['index3'] = true;
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_CAST
        );
        $configuration->add($conf);
        $this->assertEquals($expected, $configuration->get());
    }

    public function testKeyStrictException1()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1483365252);
        $this->expectExceptionMessage('Undefined configuration key "index2" for "*".');
        $confDefinition = [
            'index1' => [
                'type' => 'int'
            ]
        ];
        $conf = [
            'index1' => 123456789,
            'index2' => 123456789
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
    }

    public function testKeyStrictException2()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1483365252);
        $this->expectExceptionMessage('Undefined configuration key "index1_3" for "* > index1 > def".');
        $confDefinition = [
            'index1' => [
                'type'       => 'list',
                'definition' => [
                    'index1_1' => [
                        'type' => 'int'
                    ],
                    'index1_2' => [
                        'type' => 'float'
                    ]
                ]
            ]
        ];
        $conf = [
            'index1' => [
                'def' => [
                    'index1_1' => 456,
                    'index1_2' => 4.56,
                    'index1_3' => 4.56
                ]
            ]
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
    }

    public function testKeyStrictException3()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1483526068);
        $this->expectExceptionMessage('Empty value for "* > index2". Expected type "float".');
        $confDefinition = [
            'index1' => [
                'type' => 'int'
            ],
            'index2' => [
                'type' => 'float'
            ]
        ];
        $conf = [
            'index1' => 123456789
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
    }

    public function testKeyStrictException4()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1483526068);
        $this->expectExceptionMessage('Empty value for "* > index1 > def > index1_2". Expected type "float".');
        $confDefinition = [
            'index1' => [
                'type'       => 'list',
                'definition' => [
                    'index1_1' => [
                        'type' => 'int'
                    ],
                    'index1_2' => [
                        'type' => 'float'
                    ]
                ]
            ]
        ];
        $conf = [
            'index1' => [
                'def' => [
                    'index1_1' => 456
                ]
            ]
        ];
        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_STRICT,
            ConfigurationInterface::TYPE_STRICT
        );
        $configuration->add($conf);
    }
}
