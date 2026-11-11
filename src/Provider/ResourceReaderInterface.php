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

interface ResourceReaderInterface
{
    /**
     * @param resource|false $resource
     */
    public function setResource($resource): void;

    public function readHeader(): void;

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool elevation [m] can be false if nothing retrieved or value is unknown
     */
    public function getElevationFor(int $row, int $col): int|bool;

    /**
     * @param  float   $relativeLatitude
     * @param  float   $relativeLongitude
     * @return array{0:float,1:float} array(row, col)
     */
    public function getExactRowAndColFor(float $relativeLatitude, float $relativeLongitude): array;
}
