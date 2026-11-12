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

use Runalyze\DEM\Provider\AbstractFileProvider;

abstract class AbstractGeoTIFFProvider extends AbstractFileProvider
{
    /**
     * @throws \Runalyze\DEM\Exception\RuntimeException
     */
    protected function openResource(string $filename): void
    {
        $resource = fopen($this->PathToFiles . DIRECTORY_SEPARATOR . $filename, 'rb');

        $this->ResourceReader->setResource($resource);
        $this->ResourceReader->readHeader();

        $this->CurrentFilename = $filename;
    }

    protected function getElevationFor(int $row, int $col): int|bool
    {
        return $this->ResourceReader->getElevationFor($row, $col);
    }
}
