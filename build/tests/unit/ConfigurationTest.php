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
    public function testKeyFlexibleAndTypeCast()
    {
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
                    ],
                    'index4_2' => [
                        'type' => 'float'
                    ],
                    'index4_3' => [
                        'type'    => 'int',
                        'default' => 123
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
                        'type' => 'bool'
                    ],
                    'index5_3' => [
                        'type' => '*'
                    ],
                    'index5_4' => [
                        'type' => '*'
                    ],
                    'index5_5' => [
                        'type' => '*'
                    ],
                    'index5_6' => [
                        'type'    => 'int',
                        'default' => 123
                    ]
                ]
            ]
        ];

        $conf1 = [
            'index1' => 123456789,
            'index3' => 7.89,
            'index4' => [
                'abc' => [
                    'index4_1' => 456,
                    'index4_2' => 4.56
                ],
                'def' => [
                    'index4_1' => 156,
                    'index4_2' => 1.56
                ]
            ],
            'index5' => [
                'index5_1' => true,
                'index5_2' => false,
                'index5_3' => 55555555
            ],
            'index6' => 'hallo',
            'index7' => [
                'index7_1' => 'test',
                'index7_2' => 'test2'
            ]
        ];
        $conf2 = [
            'index3' => 5.67,
            'index4' => [
                'abc' => [
                    'index4_1' => 678
                ],
                'def' => [
                    'index4_2' => 6.78
                ]
            ],
            'index5' => [
                'index5_3' => 'abc',
                'index5_4' => 123,
                'index5_5' => 1.23
            ]
        ];

        $expected = [
            'index1' => '123456789',
            'index2' => 246,
            'index3' => 5.67,
            'index4' => [
                'abc' => [
                    'index4_1' => 678,
                    'index4_2' => 4.56,
                    'index4_3' => 123
                ],
                'def' => [
                    'index4_1' => 156,
                    'index4_2' => 6.78,
                    'index4_3' => 123
                ]
            ],
            'index5' => [
                'index5_1' => true,
                'index5_2' => false,
                'index5_3' => 'abc',
                'index5_4' => 123,
                'index5_5' => 1.23,
                'index5_6' => 123
            ],
            'index6' => 'hallo',
            'index7' => [
                'index7_1' => 'test',
                'index7_2' => 'test2'
            ]
        ];

        $configuration = new Configuration(
            $confDefinition,
            ConfigurationInterface::KEY_FLEXIBLE,
            ConfigurationInterface::TYPE_CAST
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
        $this->expectExceptionMessage('Invalid value for "index1". Expected type "int", got "float".');
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
        $this->expectExceptionMessage('Invalid value for "index1". Expected type "conf", got "float".');
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
        $this->expectExceptionMessage('Undefined configuration key "index2" for "".');
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
        $this->expectExceptionMessage('Undefined configuration key "index1_3" for "index1 > def".');
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
}
