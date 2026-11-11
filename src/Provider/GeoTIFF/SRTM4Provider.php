<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider\GeoTIFF;

use Runalyze\DEM\Exception\InvalidArgumentException;

class SRTM4Provider extends AbstractGeoTIFFProvider
{
    /** @var int */
    protected const MAX_LATITUDE = 60;

    /** @var int */
    protected const MAX_LONGITUDE = 180;

    /** @var float */
    protected const DEGREES_PER_TILE = 5.0;

    /** @var float */
    protected float $CurrentTileHorizontalReference;

    /** @var float */
    protected float $CurrentTileVerticalReference;

    /** @var float top left latitude */
    protected float $CurrentTileLatitude;

    /** @var float top left longitude */
    protected float $CurrentTileLongitude;

    /** @var string */
    protected string $FilenameFormat = 'srtm_%02d_%02d.tif';

    /** @var ResourceReaderInterface */
    protected ResourceReaderInterface $ResourceReader;

    public function initResourceReader(): void
    {
        $this->ResourceReader = new GeoTIFFReader();
    }

    /**
     * @param string $format
     */
    public function setFilenameFormat(string $format): void
    {
        $this->FilenameFormat = $format;
    }

    /**
     * @param  float                    $latitude
     * @param  float                    $longitude
     * @return string
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    protected function getFilenameFor(float $latitude, float $longitude): string
    {
        $this->loadTileReferencesFor($latitude, $longitude);

        return sprintf($this->FilenameFormat, $this->CurrentTileHorizontalReference, $this->CurrentTileVerticalReference);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     */
    protected function loadTileReferencesFor(float $latitude, float $longitude): void
    {
        if (abs(fmod($latitude, static::DEGREES_PER_TILE)) < PHP_FLOAT_EPSILON) {
            $this->CurrentTileVerticalReference = (static::MAX_LATITUDE - $latitude) / static::DEGREES_PER_TILE + 1;
        } else {
            $this->CurrentTileVerticalReference = ceil((static::MAX_LATITUDE - $latitude) / static::DEGREES_PER_TILE);
        }

        if (abs(fmod($longitude, static::DEGREES_PER_TILE)) < PHP_FLOAT_EPSILON) {
            $this->CurrentTileHorizontalReference = (static::MAX_LONGITUDE + $longitude) / static::DEGREES_PER_TILE + 1;
        } else {
            $this->CurrentTileHorizontalReference = ceil((static::MAX_LONGITUDE + $longitude) / static::DEGREES_PER_TILE);
        }

        $this->CurrentTileLatitude = static::MAX_LATITUDE - (($this->CurrentTileVerticalReference - 1) * static::DEGREES_PER_TILE);
        $this->CurrentTileLongitude = (($this->CurrentTileHorizontalReference - 1) * static::DEGREES_PER_TILE) - static::MAX_LONGITUDE;

        if ($latitude < 0) {
            $this->CurrentTileLatitude = -$this->CurrentTileLatitude;
        }
    }

    /**
     * @param  float   $latitude
     * @param  float   $longitude
     * @return array{0:float,1:float} array(row, col)
     */
    protected function getExactRowAndColFor(float $latitude, float $longitude): array
    {
        return $this->ResourceReader->getExactRowAndColFor(
            abs($this->CurrentTileLatitude - abs($latitude)) / static::DEGREES_PER_TILE,
            abs($this->CurrentTileLongitude - $longitude) / static::DEGREES_PER_TILE
        );
    }
}
