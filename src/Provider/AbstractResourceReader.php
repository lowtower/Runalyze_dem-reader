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
    /** @var \Resource|false */
    protected \Resource|false $FileResource = false;

    /**
     * @param \Resource|false $resource
     */
    public function __construct(\Resource|false $resource = false)
    {
        if (false !== $resource) {
            $this->setResource($resource);
        }
    }

    public function __destruct()
    {
        $this->closeResource();
    }

    /**
     * @param  \Resource         $resource
     * @throws RuntimeException
     */
    public function setResource(\Resource $resource): void
    {
        $this->closeResource();

        $this->FileResource = $resource;

        if (false === $this->FileResource) {
            throw new RuntimeException('Provided resource is invalid.');
        }
    }

    protected function closeResource(): void
    {
        if ($this->FileResource instanceof \Resource) {
            fclose($this->FileResource);
        }
    }

    abstract public function readHeader(): void;

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool elevation [m] can be false if nothing retrieved or value is unknown
     */
    abstract public function getElevationFor(int $row, int $col): int|bool;
}
