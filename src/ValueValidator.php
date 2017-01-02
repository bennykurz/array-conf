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

/**
 * @author Viktor Firus <v@n86.io>
 * @since  1.0.0
 */
class ValueValidator
{
    /**
     * This method cast type of value or thrown an error, if the value type isn't how it's defined in
     * configuration-definition.
     *
     * @param mixed $value      The value, which should check or cast.
     * @param mixed $key        Configuration key of given value.
     * @param array $definition The definition, where the given key is defined.
     * @param array $keyPath    The path to the given key, so that in case of an exception, it's possible to backtrace,
     *                          where the configuration error is.
     * @param bool  $castType   If true, the value will be only cast to right type. If false and the value has wrong
     *                          type, an exception will be thrown.
     *
     * @throws InvalidConfigurationException
     *
     * @internal
     */
    public static function checkValueType(&$value, $key, array $definition, array $keyPath, bool $castType)
    {
        $type = $definition[$key]['type'];

        if ($castType) {
            self::castType($type, $value);

            return;
        }

        self::strictType($type, $value, $keyPath);
    }

    /**
     * @param string $type
     * @param mixed  $value
     * @param array  $keyPath
     */
    private static function strictType(string $type, $value, array $keyPath)
    {
        self::strictConf($type, $value, $keyPath);
        self::strictValue($type, $value, $keyPath);
    }

    /**
     * @param string $type
     * @param mixed  $value
     * @param array  $keyPath
     *
     * @throws InvalidConfigurationException
     */
    private static function strictConf(string $type, $value, array $keyPath)
    {
        if (TypeValidator::isTypeConf($type) && !is_array($value)) {
            throw InvalidConfigurationException::invalidValue($keyPath, $type, gettype($value));
        }
    }

    /**
     * @param string $type
     * @param mixed  $value
     * @param array  $keyPath
     *
     * @throws InvalidConfigurationException
     */
    private static function strictValue(string $type, $value, array $keyPath)
    {
        if ($type === 'bool' && !is_bool($value) ||
            $type === 'int' && !is_int($value) ||
            $type === 'float' && !is_float($value) ||
            $type === 'string' && !is_string($value)
        ) {
            throw InvalidConfigurationException::invalidValue($keyPath, $type, gettype($value));
        }
    }

    /**
     * @param string $type
     * @param mixed  $value
     */
    private static function castType(string $type, &$value)
    {
        if (TypeValidator::isTypeWildcard($type) || TypeValidator::isTypeConf($type)) {
            return;
        }
        settype($value, $type);
    }

    /**
     * Check if valid type definition exists for given key.
     *
     * If flexible key is true and no valid type exists, the type will be override with '*' or 'conf'. '*' if the value
     * isn't an array and conf if value is an array. That means also, that if no definition exists for an key, the
     * definition will be created.
     * If flexible key is false, it will be always thrown an error, if no valid type exist.
     *
     * @param mixed $value
     * @param mixed $key
     * @param array $definition
     * @param array $keyPath
     *
     * @param bool  $flexibleKey
     *
     * @throws InvalidConfigurationException
     *
     * @internal
     */
    public static function checkValueDefinition($value, $key, array &$definition, array $keyPath, bool $flexibleKey)
    {
        $validType = TypeValidator::hasValidType($definition, $key);

        if (!$validType && !$flexibleKey) {
            $keyPath = array_slice($keyPath, 0, count($keyPath) - 1);
            throw InvalidConfigurationException::undefinedConfigurationKey($key, $keyPath);
        }

        if (!$validType && is_array($value)) {
            $definition[$key] = [
                'type'       => 'conf',
                'definition' => []
            ];

            return;
        }

        if (!$validType) {
            $definition[$key] = [
                'type' => '*'
            ];
        }
    }
}
