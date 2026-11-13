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

use Runalyze\DEM\Exception\InvalidArgumentException;

final class ArgumentsCheckTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ReflectionMethod
     */
    protected $CheckArgumentsMethod;

    /**
     * @var object
     */
    protected $Object;

    protected function setUp(): void
    {
        $this->Object = new class {
            use \Runalyze\DEM\Interpolation\ArgumentsCheckTrait;
        };
        $this->CheckArgumentsMethod = new \ReflectionMethod($this->Object, 'checkArguments');
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testValidCheckArgumentsCalls(): void
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.0, 0.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 0.0, 1.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 1.0, 0.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 1.0, 1.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.3, [42, 42, 42, 42]);
    }

    public function testEmptyBoundingBox(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, []);
    }

    public function testBoundingBoxWithOnlyThreeElements(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, [1, 2, 3]);
    }

    public function testBoundingBoxWithTooManyElements(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, [1, 2, 3, 4, 5]);
    }

    public function testXNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, -0.1, 0.5, [1, 2, 3, 4]);
    }

    public function testXLargerThanOne(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 1.1, 0.5, [1, 2, 3, 4]);
    }

    public function testYNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.1, -1.0, [1, 2, 3, 4]);
    }

    public function testYLargerThanOne(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.3, 1.001, [1, 2, 3, 4]);
    }
}
