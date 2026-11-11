<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM;

use Runalyze\DEM\Exception\InvalidArgumentException;

interface ReaderInterface
{
    /**
     * @param  array<array{0:float,1:float}> $latitudeLongitudes array(array($lat, $lng), ...)
     * @return bool
     */
    public function hasDataFor(array $latitudeLongitudes): bool;

    /**
     * @param  array<float>             $latitudes
     * @param  array<float>             $longitudes
     * @return array<int|bool>          elevations [m] can be false if nothing retrieved
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    public function getElevations(array $latitudes, array $longitudes): array;
}
