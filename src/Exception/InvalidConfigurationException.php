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

namespace N86io\ArrayConf\Exception;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  1.0.0
 */
class InvalidConfigurationException extends \Exception
{
    /**
     * @param mixed $key
     * @param array $keyPath
     *
     * @return InvalidConfigurationException
     */
    public static function undefinedConfigurationKey($key, array $keyPath)
    {
        return new self(
            'Undefined configuration key "' . $key . '" for "' . implode(' > ', $keyPath) . '".',
            1483365252
        );
    }

    /**
     * @param array  $keyPath
     * @param string $expectedType
     * @param string $gotType
     *
     * @return InvalidConfigurationException
     */
    public static function invalidValue(array $keyPath, string $expectedType, string $gotType)
    {
        $gotType = $gotType === 'double' ? 'float' : $gotType;

        return new self(
            'Invalid value for "' . implode(' > ', $keyPath) . '". Expected type "' . $expectedType . '", ' .
            'got "' . $gotType . '".',
            1483365239
        );
    }

    /**
     * @param array  $keyPath
     * @param string $expectedType
     *
     * @return InvalidConfigurationException
     */
    public static function keyNotFound(array $keyPath = [], string $expectedType = '')
    {
        return new self(
            'Empty value for "' . implode(' > ', $keyPath) . '". Expected type "' . $expectedType . '".',
            1483526068
        );
    }
}
