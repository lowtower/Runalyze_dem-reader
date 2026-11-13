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

namespace Runalyze\DEM\Tests\Provider;

use Runalyze\DEM\Exception\InvalidArgumentException;
use Runalyze\DEM\Provider\AbstractFileProvider;

final class AbstractFileProviderTest extends \PHPUnit\Framework\TestCase
{
    protected \PHPUnit\Framework\MockObject\MockObject $Object;

    protected function setUp(): void
    {
        $this->Object = $this->getMockForAbstractClass(\Runalyze\DEM\Provider\AbstractFileProvider::class, ['path/to/files']);
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Object->hasDataFor(['foo', 'bar']);
    }

    public function testTooManyCoordinatesInLocationArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Object->hasDataFor([[47.7, 47.6, 47.5]]);
    }

    public function testThatTheProviderHasNoDataAsThereAreNoFiles(): void
    {
        $this->Object->method('getFilenameFor')->willReturn('does-not-exist.tif');

        $this->assertFalse($this->Object->hasDataFor([[49.4, 7.7]]));
    }

    public function testThatGetElevationsRequiresEqualSizedArrays(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Object->getElevations([1, 2, 3], [1, 2, 3, 4]);
    }

    public function testUnknownLocation(): void
    {
        $this->assertFalse($this->Object->getElevation(0.0, 0.0));
    }

    public function testInvalidLocation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->assertFalse($this->Object->getElevation(123.4, 321.0));
    }
}
