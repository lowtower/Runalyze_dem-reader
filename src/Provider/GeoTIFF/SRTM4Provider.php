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

namespace Runalyze\DEM\Provider\GeoTIFF;

use Runalyze\DEM\Provider\ResourceReaderInterface;

class SRTM4Provider extends AbstractGeoTIFFProvider
{
    protected const int MAX_LATITUDE = 60;

    protected const int MAX_LONGITUDE = 180;

    protected const float DEGREES_PER_TILE = 5.0;

    protected float $CurrentTileHorizontalReference;

    protected float $CurrentTileVerticalReference;

    /**
     * @var float top left latitude
     */
    protected float $CurrentTileLatitude;

    /**
     * @var float top left longitude
     */
    protected float $CurrentTileLongitude;

    protected string $FilenameFormat = 'srtm_%02d_%02d.tif';

    protected ResourceReaderInterface $ResourceReader;

    protected function initResourceReader(): void
    {
        $this->ResourceReader = new GeoTIFFReader();
    }

    public function setFilenameFormat(string $format): void
    {
        $this->FilenameFormat = $format;
    }

    /**
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    protected function getFilenameFor(float $latitude, float $longitude): string
    {
        $this->loadTileReferencesFor($latitude, $longitude);

        return sprintf($this->FilenameFormat, $this->CurrentTileHorizontalReference, $this->CurrentTileVerticalReference);
    }

    protected function loadTileReferencesFor(float $latitude, float $longitude): void
    {
        if (abs(fmod($latitude, self::DEGREES_PER_TILE)) < PHP_FLOAT_EPSILON) {
            $this->CurrentTileVerticalReference = (self::MAX_LATITUDE - $latitude) / self::DEGREES_PER_TILE + 1;
        } else {
            $this->CurrentTileVerticalReference = ceil((self::MAX_LATITUDE - $latitude) / self::DEGREES_PER_TILE);
        }

        if (abs(fmod($longitude, self::DEGREES_PER_TILE)) < PHP_FLOAT_EPSILON) {
            $this->CurrentTileHorizontalReference = (self::MAX_LONGITUDE + $longitude) / self::DEGREES_PER_TILE + 1;
        } else {
            $this->CurrentTileHorizontalReference = ceil((self::MAX_LONGITUDE + $longitude) / self::DEGREES_PER_TILE);
        }

        $this->CurrentTileLatitude = self::MAX_LATITUDE - (($this->CurrentTileVerticalReference - 1) * self::DEGREES_PER_TILE);
        $this->CurrentTileLongitude = (($this->CurrentTileHorizontalReference - 1) * self::DEGREES_PER_TILE) - self::MAX_LONGITUDE;

        if ($latitude < 0) {
            $this->CurrentTileLatitude = -$this->CurrentTileLatitude;
        }
    }

    /**
     * @return array{0:float,1:float} array(row, col)
     */
    protected function getExactRowAndColFor(float $latitude, float $longitude): array
    {
        return $this->ResourceReader->getExactRowAndColFor(
            abs($this->CurrentTileLatitude - abs($latitude)) / self::DEGREES_PER_TILE,
            abs($this->CurrentTileLongitude - $longitude) / self::DEGREES_PER_TILE
        );
    }
}
