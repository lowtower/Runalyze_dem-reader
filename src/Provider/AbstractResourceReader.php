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

use Runalyze\DEM\Exception\RuntimeException;

abstract class AbstractResourceReader implements ResourceReaderInterface
{
    /**
     * @var resource|false
     */
    protected $FileResource = false;

    /**
     * @param resource|false $resource
     */
    public function __construct($resource = false)
    {
        if ($resource !== false) {
            $this->setResource($resource);
        }
    }

    public function __destruct()
    {
        $this->closeResource();
    }

    /**
     * @param resource|false $resource
     * @throws \Runalyze\DEM\Exception\RuntimeException
     */
    public function setResource($resource): void
    {
        $this->closeResource();

        $this->FileResource = $resource;

        if (! is_resource($this->FileResource)) {
            throw new RuntimeException('Provided resource is invalid.');
        }
    }

    protected function closeResource(): void
    {
        if (is_resource($this->FileResource)) {
            fclose($this->FileResource);
        }
    }

    abstract public function readHeader(): void;

    /**
     * @return int|bool elevation [m] can be false if nothing retrieved or value is unknown
     */
    abstract public function getElevationFor(int $row, int $col): int|bool;

    /**
     * @return array{0:float,1:float} array(row, col)
     */
    abstract public function getExactRowAndColFor(float $relativeLatitude, float $relativeLongitude): array;
}
