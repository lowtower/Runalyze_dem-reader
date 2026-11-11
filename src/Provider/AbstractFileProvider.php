<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider;

use Runalyze\DEM\Exception\InvalidArgumentException;
use Runalyze\DEM\Interpolation\InterpolationInterface;

abstract class AbstractFileProvider implements ProviderInterface
{
    use GuessInvalidValuesTrait;

    /** @var int */
    protected const int MAX_LATITUDE = 90;

    /** @var int */
    protected const int MAX_LONGITUDE = 180;

    /** @var string */
    protected string $PathToFiles;

    /** @var \Resource|false */
    protected \Resource|false $FileResource = false;

    /** @var string|false */
    protected string|false $CurrentFilename = false;

    /** @var InterpolationInterface|null */
    protected ?InterpolationInterface $Interpolation = null;

    /** @var ResourceReaderInterface */
    protected ResourceReaderInterface $ResourceReader;

    /**
     * @param string                       $pathToFiles
     * @param InterpolationInterface|null  $interpolation
     */
    public function __construct(string $pathToFiles, ?InterpolationInterface $interpolation = null)
    {
        $this->PathToFiles = $pathToFiles;
        $this->Interpolation = $interpolation;

        $this->addSlashToPathIfNotThere();
        $this->initResourceReader();
    }

    private function addSlashToPathIfNotThere(): void
    {
        if (!str_ends_with($this->PathToFiles, '/')) {
            $this->PathToFiles .= '/';
        }
    }

    /**
     * @param InterpolationInterface $interpolation
     */
    public function setInterpolation(InterpolationInterface $interpolation): void
    {
        $this->Interpolation = $interpolation;
    }

    public function removeInterpolation(): void
    {
        $this->Interpolation = null;
    }

    /**
     * @param  array                    $latitudeLongitudes array(array($lat, $lng), ...)
     * @return bool
     * @throws InvalidArgumentException
     */
    public function hasDataFor(array $latitudeLongitudes): bool
    {
        foreach ($latitudeLongitudes as $location) {
            if (!is_array($location) || count($location) !== 2) {
                throw new InvalidArgumentException('Locations must be arrays of two values.');
            }

            if (!$this->locationIsInBounds($location[0], $location[1])) {
                return false;
            }

            if (!file_exists($this->PathToFiles.$this->getFilenameFor($location[0], $location[1]))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  float $latitude
     * @param  float $longitude
     * @return bool
     */
    protected function locationIsInBounds(float $latitude, float $longitude): bool
    {
        return
            ($latitude > -static::MAX_LATITUDE) && ($latitude <= static::MAX_LATITUDE) &&
            ($longitude > -static::MAX_LONGITUDE) && ($longitude < static::MAX_LONGITUDE)
        ;
    }

    /**
     * @return bool
     */
    public function usesInterpolation(): bool
    {
        return null !== $this->Interpolation;
    }

    /**
     * @param  float[]                   $latitudes
     * @param  float[]                   $longitudes
     * @return array                     elevations [m] can be false if nothing retrieved
     * @throws InvalidArgumentException
     */
    public function getElevations(array $latitudes, array $longitudes): array
    {
        $numberOfPoints = count($latitudes);
        $elevations = [];

        if (count($longitudes) !== $numberOfPoints) {
            throw new InvalidArgumentException('Arrays for latitude and longitude must be of same size.');
        }

        for ($i = 0; $i < $numberOfPoints; ++$i) {
            $elevations[] = $this->getElevation($latitudes[$i], $longitudes[$i]);
        }

        return $elevations;
    }

    /**
     * Get elevation for specified location.
     *
     * Availability of data must be checked in advance via hasDataFor($locations).
     * Only 'invalid' locations such as (0.0, 0.0) won't throw an exception but return false.
     *
     * @param  float                     $latitude
     * @param  float                     $longitude
     * @return int|bool                  elevation [m] can be false if nothing retrieved
     * @throws InvalidArgumentException
     */
    public function getElevation($latitude, $longitude): int|bool
    {
        if ($latitude === 0.0 && $longitude === 0.0) {
            return false;
        }

        if (!$this->locationIsInBounds($latitude, $longitude)) {
            throw new InvalidArgumentException(
                sprintf('Location (%f, %f) is out of bounds ([-%f, %f], [-%f, %f]).',
                    $latitude, $longitude,
                    static::MAX_LATITUDE, static::MAX_LATITUDE,
                    static::MAX_LONGITUDE, static::MAX_LONGITUDE
                )
            );
        }

        $filename = $this->getFilenameFor($latitude, $longitude);

        if ($this->CurrentFilename !== $filename) {
            $this->openResource($filename);
        }

        return $this->getElevationFromResource($latitude, $longitude);
    }

    /**
     * @param  float    $latitude
     * @param  float    $longitude
     * @return int|bool can be false if nothing retrieved
     */
    protected function getElevationFromResource(float $latitude, float $longitude): int|bool
    {
        [$exactRowValue, $exactColValue] = $this->getExactRowAndColFor($latitude, $longitude);

        if (!$this->usesInterpolation()) {
            return $this->getElevationFor((int) round($exactRowValue), (int) round($exactColValue));
        }

        return $this->getInterpolatedElevationFor($exactRowValue, $exactColValue);
    }

    /**
     * @param  float    $exactRowValue
     * @param  float    $exactColValue
     * @return int|bool can be false if nothing retrieved
     */
    protected function getInterpolatedElevationFor(float $exactRowValue, float $exactColValue): int|bool
    {
        $row = (int) floor($exactRowValue);
        $col = (int) floor($exactColValue);

        $elevationOnBoundingBox = [
            $this->getElevationFor($row, $col),
            $this->getElevationFor($row, $col + 1),
            $this->getElevationFor($row + 1, $col),
            $this->getElevationFor($row + 1, $col + 1),
        ];

        $this->guessInvalidValuesOnBox($elevationOnBoundingBox);

        return $this->Interpolation->interpolate(
            ($exactColValue - $col),
            ($exactRowValue - $row),
            $elevationOnBoundingBox
        );
    }

    /**
     * @param  float  $latitude
     * @param  float  $longitude
     * @return string
     */
    abstract protected function getFilenameFor(float $latitude, float $longitude): string;

    abstract protected function initResourceReader(): void;

    /**
     * @param string $filename
     */
    abstract protected function openResource(string $filename): void;

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool elevation [m] can be false if nothing retrieved
     */
    abstract protected function getElevationFor(int $row, int $col): int|bool;

    /**
     * @param  float $latitude
     * @param  float $longitude
     * @return array array(row, col)
     */
    abstract protected function getExactRowAndColFor(float $latitude, float $longitude): array;
}
