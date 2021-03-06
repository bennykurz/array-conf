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

namespace N86io\ArrayConf;

use N86io\ArrayConf\Exception\InvalidConfigurationException;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  1.0.0
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $definition;

    /**
     * @var int
     */
    private $flexibleKey;

    /**
     * @var int
     */
    private $castType;

    /**
     * @var array
     */
    private $configuration = [];

    /**
     * @param array $definition
     * @param int   $keyMode
     * @param int   $typeMode
     */
    public function __construct(
        array $definition,
        int $keyMode = self::KEY_FLEXIBLE,
        int $typeMode = self::TYPE_CAST
    ) {
        Assert::oneOf($keyMode, [self::KEY_FLEXIBLE, self::KEY_STRICT]);
        Assert::oneOf($typeMode, [self::TYPE_CAST, self::TYPE_STRICT]);
        $this->definition = $definition;
        $this->flexibleKey = $keyMode === self::KEY_FLEXIBLE;
        $this->castType = $typeMode === self::TYPE_CAST;
    }

    public function add(array $addConfiguration): ConfigurationInterface
    {
        $this->addToConfiguration($this->configuration, $addConfiguration, $this->definition, ['*']);

        return $this;
    }

    public function get()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     * @param array $addConfiguration
     * @param array $definition
     * @param array $keyPath
     */
    private function addToConfiguration(
        array &$configuration,
        array $addConfiguration,
        array &$definition,
        array $keyPath
    ) {
        foreach ($addConfiguration as $key => $value) {
            $confKeyPath = $keyPath;
            $confKeyPath[] = $key;

            ValueValidator::checkValueDefinition($value, $key, $definition, $confKeyPath, $this->flexibleKey);
            ValueValidator::checkValueType($value, $key, $definition, $confKeyPath, $this->castType);

            $type = $definition[$key]['type'];

            if ($this->addValue($configuration, $key, $value, $type)) {
                continue;
            }

            if (empty($configuration[$key])) {
                $configuration[$key] = [];
            }

            if ($this->addSingleConf($configuration, $key, $value, $type, $definition, $confKeyPath)) {
                continue;
            }

            $this->addListConf($configuration, $key, $value, $definition, $confKeyPath);
        }

        $this->addDefault($configuration, $definition);
        $this->checkMissingKeys($configuration, $definition, $keyPath);
    }

    /**
     * @param array  $configuration
     * @param mixed  $key
     * @param mixed  $value
     * @param string $type
     *
     * @return bool
     */
    private function addValue(array &$configuration, $key, $value, string $type): bool
    {
        if (TypeValidator::isTypeWildcard($type) || TypeValidator::isTypeValue($type)) {
            $configuration[$key] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param array  $configuration
     * @param mixed  $key
     * @param mixed  $value
     * @param string $type
     * @param array  $definition
     * @param array  $keyPath
     *
     * @return bool
     */
    private function addSingleConf(
        array &$configuration,
        $key,
        $value,
        string $type,
        array $definition,
        array $keyPath
    ): bool {
        if (TypeValidator::isTypeSingleConf($type)) {
            $this->addToConfiguration($configuration[$key], $value, $definition[$key]['definition'], $keyPath);

            return true;
        }

        return false;
    }

    /**
     * @param array $configuration
     * @param mixed $key
     * @param mixed $value
     * @param array $definition
     * @param array $keyPath
     */
    private function addListConf(
        array &$configuration,
        $key,
        $value,
        array $definition,
        array $keyPath
    ) {
        $onlyNumeric = $this->hasOnlyNumericKeys($value) && $this->hasOnlyNumericKeys($configuration[$key]);
        $highestIndex = null;
        if ($onlyNumeric) {
            $highestIndex = $this->getHighestIndex($configuration[$key]);
        }

        foreach ($value as $listConfKey => $listConf) {
            if ($onlyNumeric) {
                $highestIndex++;
                $listConfKey = $highestIndex;
            }
            $listConfKeyPath = $keyPath;
            $listConfKeyPath[] = $listConfKey;

            if (empty($configuration[$key][$listConfKey])) {
                $configuration[$key][$listConfKey] = [];
            }

            $this->addToConfiguration(
                $configuration[$key][$listConfKey],
                $listConf,
                $definition[$key]['definition'],
                $listConfKeyPath
            );
        }
    }

    /**
     * Add default values to configuration, if no value is set.
     *
     * @param array $configuration
     * @param array $definition
     */
    private function addDefault(array &$configuration, array $definition)
    {
        foreach ($definition as $key => $item) {
            if (TypeValidator::isTypeConf($item['type']) || empty($item['default'])) {
                continue;
            }
            if (isset($configuration[$key])) {
                continue;
            }
            $configuration[$key] = $item['default'];
        }
    }

    /**
     * If key-mode is strict, check if a key missed and thrown an exception if that case is true.
     *
     * @param array $configuration
     * @param array $definition
     * @param array $keyPath
     *
     * @throws InvalidConfigurationException
     */
    private function checkMissingKeys(array $configuration, array $definition, array $keyPath)
    {
        if ($this->flexibleKey) {
            return;
        }

        foreach ($definition as $key => $item) {
            if (TypeValidator::isTypeConf($item['type'])) {
                continue;
            }
            if (isset($configuration[$key])) {
                continue;
            }
            $defKeyPath = $keyPath;
            $defKeyPath[] = $key;
            throw InvalidConfigurationException::keyNotFound($defKeyPath, $item['type']);
        }
    }

    /**
     * Returns true, if array keys are only numeric.
     *
     * @param array $array
     *
     * @return bool
     */
    private function hasOnlyNumericKeys(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) === 0;
    }

    /**
     * Returns highest index of array. If array contains no elements, then returns -1.
     *
     * @param array $array
     *
     * @return int
     */
    private function getHighestIndex(array $array): int
    {
        $keys = array_keys($array);
        if (count($keys) === 0) {
            return -1;
        }

        return max($keys);
    }
}
