<?php

declare(strict_types=1);

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests\Provider\Fixtures;

use Runalyze\DEM\Interpolation\InterpolationInterface;
use Runalyze\DEM\Provider\ProviderInterface;

class ProviderReturningTheTruth implements ProviderInterface
{
    /**
     * @param  array $latitudeLongitudes array(array($lat, $lng), ...)
     */
    public function hasDataFor(array $latitudeLongitudes): bool
    {
        return true;
    }

    public function setInterpolation(InterpolationInterface $interpolation): void {}

    /**
     * @param  float    $latitude
     * @param  float    $longitude
     * @return int|bool elevation [m] can be false if nothing retrieved
     */
    public function getElevation($latitude, $longitude): int|bool
    {
        return 42;
    }

    /**
     * @param  float[] $latitudes
     * @param  float[] $longitudes
     * @return array   elevations [m] can be false if nothing retrieved
     */
    public function getElevations(array $latitudes, array $longitudes): array
    {
        return array_fill(0, count($latitudes), 42);
    }
}
