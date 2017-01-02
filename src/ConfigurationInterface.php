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
interface ConfigurationInterface
{
    const KEY_FLEXIBLE = 1;

    const KEY_STRICT = 2;

    const TYPE_CAST = 3;

    const TYPE_STRICT = 4;

    public function add(array $configuration);

    public function get();
}
