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

namespace Runalyze\DEM\Tests\Interpolation;

use Runalyze\DEM\Interpolation\LambdaInterpolation;

final class LambdaInterpolationTest extends \PHPUnit\Framework\TestCase
{
    public function testInvalidClosure(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new LambdaInterpolation(fn (): int => 1);
    }

    public function testThatInvalidCallsAreDetected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $Interpolation = new LambdaInterpolation(fn ($x, $y, array $elevationOnBoundingBox): int => 1);

        $Interpolation->interpolate(0.5, 0.14, [1, 2, 3]);
    }

    public function testConstantClosure(): void
    {
        $Interpolation = new LambdaInterpolation(fn ($x, $y, array $elevationOnBoundingBox): int => 42);

        $this->assertSame(42, $Interpolation->interpolate(0.314, 0.42, [100, 100, 100, 100]));
    }
}
