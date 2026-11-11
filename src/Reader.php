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
    /** @var ProviderInterface[] */
    protected array $Provider = [];

    /** @var bool */
    protected bool $InvalidFlag = false;

    /**
     * @param ProviderInterface|null $provider
     */
    public function __construct(?ProviderInterface $provider = null)
    {
        if (null !== $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider): void
    {
        $this->Provider[] = $provider;
    }

    /**
     * @return int
     */
    public function numberOfProviders(): int
    {
        return count($this->Provider);
    }

    /**
     * @return bool
     */
    public function hasProviders(): bool
    {
        return !empty($this->Provider);
    }

    /**
     * @param  array $latitudeLongitudes array(array($lat, $lng), ...)
     * @return bool
     */
    public function hasDataFor(array $latitudeLongitudes): bool
    {
        foreach ($this->Provider as $provider) {
            if ($provider->hasDataFor($latitudeLongitudes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array            $latitudes
     * @param  array            $longitudes
     * @return array            elevations [m] can be false if nothing retrieved
     * @throws RuntimeException
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
     * @param  float[] $latitudes
     * @param  float[] $longitudes
     * @return array   array(array($lat, $lng), ...)
     */
    protected function getBoundsFor(array $latitudes, array $longitudes): array
    {
        $filteredLatitudes = array_filter($latitudes);
        $filteredLongitudes = array_filter($longitudes);

        if (empty($filteredLatitudes) || empty($filteredLongitudes)) {
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
