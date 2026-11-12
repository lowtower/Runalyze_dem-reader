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

class SRTM1ArcSecondProvider extends SRTM4Provider
{
    protected const float DEGREES_PER_TILE = 1.0;

    protected string $FilenameFormat = '%s%02d_%s%03d.tif';

    /**
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    protected function getFilenameFor(float $latitude, float $longitude): string
    {
        $this->loadTileReferencesFor($latitude, $longitude);

        return sprintf(
            $this->FilenameFormat,
            $this->CurrentTileVerticalReference < 0 ? 's' : 'n',
            abs($this->CurrentTileVerticalReference),
            $this->CurrentTileHorizontalReference < 0 ? 'w' : 'e',
            abs($this->CurrentTileHorizontalReference)
        );
    }

    protected function loadTileReferencesFor(float $latitude, float $longitude): void
    {
        $this->CurrentTileVerticalReference = floor($latitude / self::DEGREES_PER_TILE);
        $this->CurrentTileHorizontalReference = floor($longitude / self::DEGREES_PER_TILE);

        $this->CurrentTileLatitude = $this->CurrentTileVerticalReference;
        $this->CurrentTileLongitude = $this->CurrentTileHorizontalReference;
    }

    /**
     * @return array{0:float,1:float} array(row, col)
     */
    protected function getExactRowAndColFor(float $latitude, float $longitude): array
    {
        return $this->ResourceReader->getExactRowAndColFor(
            abs($this->CurrentTileLatitude + 1 - $latitude) / self::DEGREES_PER_TILE,
            abs($longitude - $this->CurrentTileLongitude) / self::DEGREES_PER_TILE
        );
    }
}
