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

namespace Runalyze\DEM\Interpolation;

use Runalyze\DEM\Exception\InvalidArgumentException;

trait ArgumentsCheckTrait
{
    /**
     * @param  array<int|bool>          $elevationOnBoundingBox elevation data on [p0, p1, p2, p3]
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    protected function checkArguments(float $x, float $y, array $elevationOnBoundingBox): void
    {
        if (count($elevationOnBoundingBox) !== 4) {
            throw new InvalidArgumentException('Array with elevation on bounding box must have four values.');
        }

        if ($x < 0.0 || $x > 1.0) {
            throw new InvalidArgumentException('$x must be within [0.0, 1.0]');
        }

        if ($y < 0.0 || $y > 1.0) {
            throw new InvalidArgumentException('$y must be within [0.0, 1.0]');
        }
    }
}
