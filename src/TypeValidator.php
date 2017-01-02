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

/**
 * @author Viktor Firus <v@n86.io>
 * @since  1.0.0
 */
class TypeValidator
{
    /**
     * If type of definition is valid, returns true, otherwise false.
     *
     * @param array $definition
     * @param mixed $key
     *
     * @return bool
     *
     * @internal
     */
    public static function hasValidType(array $definition, $key): bool
    {
        $type = isset($definition[$key]) && isset($definition[$key]['type']) ? $definition[$key]['type'] : '-';

        return self::isTypeWildcard($type) || self::isTypeValue($type) || self::isTypeConf($type);
    }

    /**
     * @param $type
     *
     * @return bool
     *
     * @internal
     */
    public static function isTypeWildcard($type): bool
    {
        return $type === '*';
    }

    /**
     * @param $type
     *
     * @return bool
     *
     * @internal
     */
    public static function isTypeValue($type): bool
    {
        return $type === 'bool' || $type === 'int' || $type === 'float' || $type === 'string';
    }

    /**
     * @param $type
     *
     * @return bool
     *
     * @internal
     */
    public static function isTypeConf($type): bool
    {
        return self::isTypeListConf($type) || self::isTypeSingleConf($type);
    }

    /**
     * @param $type
     *
     * @return bool
     *
     * @internal
     */
    public static function isTypeListConf($type): bool
    {
        return $type === 'list';
    }

    /**
     * @param $type
     *
     * @return bool
     *
     * @internal
     */
    public static function isTypeSingleConf($type): bool
    {
        return $type === 'conf';
    }
}
