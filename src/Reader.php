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

use Runalyze\DEM\Exception\RuntimeException;
use Runalyze\DEM\Provider\ProviderInterface;

class Reader implements ReaderInterface
{
    /**
     * @var ProviderInterface[]
     */
    protected array $Provider = [];

    protected bool $InvalidFlag = false;

    public function __construct(?ProviderInterface $provider = null)
    {
        if ($provider instanceof \Runalyze\DEM\Provider\ProviderInterface) {
            $this->addProvider($provider);
        }
    }

    public function addProvider(ProviderInterface $provider): void
    {
        $this->Provider[] = $provider;
    }

    public function numberOfProviders(): int
    {
        return count($this->Provider);
    }

    public function hasProviders(): bool
    {
        return $this->Provider !== [];
    }

    /**
     * @param  array<array{0:float,1:float}> $latitudeLongitudes array(array($lat, $lng), ...)
     * @throws \Runalyze\DEM\Exception\RuntimeException
     */
    public function hasDataFor(array $latitudeLongitudes): bool
    {
        return array_any($this->Provider, fn (\Runalyze\DEM\Provider\ProviderInterface $provider): bool => $provider->hasDataFor($latitudeLongitudes));
    }

    /**
     * @param  array<float>     $latitudes
     * @param  array<float>     $longitudes
     * @return array<int|bool>  elevations [m] can be false if nothing retrieved
     * @throws \Runalyze\DEM\Exception\RuntimeException
     */
    public function getElevations(array $latitudes, array $longitudes): array
    {
        $boundaries = $this->getBoundsFor($latitudes, $longitudes);

        foreach ($this->Provider as $provider) {
            if ($provider->hasDataFor($boundaries)) {
                return $provider->getElevations($latitudes, $longitudes);
            }
        }

        throw new RuntimeException('No provider can handle the given locations.');
    }

    /**
     * @param  array<float> $latitudes
     * @param  array<float> $longitudes
     * @return array<array{0:float,1:float}>   array(array($lat, $lng), ...)
     */
    protected function getBoundsFor(array $latitudes, array $longitudes): array
    {
        $filteredLatitudes = array_filter($latitudes);
        $filteredLongitudes = array_filter($longitudes);

        if ($filteredLatitudes === [] || $filteredLongitudes === []) {
            return [];
        }

        $minLatitude = min($filteredLatitudes);
        $maxLatitude = max($filteredLatitudes);
        $minLongitude = min($filteredLongitudes);
        $maxLongitude = max($filteredLongitudes);

        return [
            [$minLatitude, $minLongitude],
            [$minLatitude, $maxLongitude],
            [$maxLatitude, $minLongitude],
            [$maxLatitude, $maxLongitude],
        ];
    }
}
