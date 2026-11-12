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

namespace Runalyze\DEM\tests\Interpolation;

use Runalyze\DEM\Interpolation\BilinearInterpolation;

final class BilinearInterpolationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BilinearInterpolation
     */
    protected $Interpolation;

    protected function setUp(): void
    {
        $this->Interpolation = new BilinearInterpolation();
    }

    public function testBoundaryPoints(): void
    {
        $boundingBox = [3, 42, 1337, 666];

        $this->assertEqualsWithDelta(3.0, $this->Interpolation->interpolate(0.0, 0.0, $boundingBox), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(42.0, $this->Interpolation->interpolate(1.0, 0.0, $boundingBox), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1337.0, $this->Interpolation->interpolate(0.0, 1.0, $boundingBox), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(666.0, $this->Interpolation->interpolate(1.0, 1.0, $boundingBox), PHP_FLOAT_EPSILON);
    }

    public function testPointsOnBoundaryLines(): void
    {
        $boundingBox = [10, 20, 30, 40];

        $this->assertEquals(15, $this->Interpolation->interpolate(0.5, 0.0, $boundingBox));
        $this->assertEquals(20, $this->Interpolation->interpolate(0.0, 0.5, $boundingBox));
        $this->assertEquals(30, $this->Interpolation->interpolate(1.0, 0.5, $boundingBox));
        $this->assertEquals(35, $this->Interpolation->interpolate(0.5, 1.0, $boundingBox));
    }

    public function testCenter(): void
    {
        $this->assertEquals(20, $this->Interpolation->interpolate(0.5, 0.5, [20, 20, 20, 20]));
        $this->assertEquals(15, $this->Interpolation->interpolate(0.5, 0.5, [10, 10, 20, 20]));
        $this->assertEquals(15, $this->Interpolation->interpolate(0.5, 0.5, [20, 20, 10, 10]));
        $this->assertEquals(15, $this->Interpolation->interpolate(0.5, 0.5, [10, 20, 10, 20]));
        $this->assertEquals(15, $this->Interpolation->interpolate(0.5, 0.5, [20, 10, 20, 10]));
        $this->assertEquals(75, $this->Interpolation->interpolate(0.5, 0.5, [100, 100, 100, 0]));
        $this->assertEquals(38, $this->Interpolation->interpolate(0.5, 0.5, [10, 42, 31, 69]));
    }
}
