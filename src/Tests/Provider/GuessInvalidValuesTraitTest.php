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

final class GuessInvalidValuesTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ReflectionMethod
     */
    protected $GuessInvalidValuesOnBoxMethod;

    /**
     * @var object
     */
    protected $Object;

    protected function setUp(): void
    {
        $this->Object = new class {
            use \Runalyze\DEM\Provider\GuessInvalidValuesTrait;
        };
        $this->GuessInvalidValuesOnBoxMethod = new \ReflectionMethod($this->Object, 'guessInvalidValuesOnBox');
    }

    public function testEmptyBoundingBox(): void
    {
        $box = [false, false, false, false];
        $this->GuessInvalidValuesOnBoxMethod->invokeArgs($this->Object, [&$box]);
        $this->assertEquals([false, false, false, false], $box);
    }

    public function testBoundingBoxWithAllValidEntries(): void
    {
        $box = [42, 42, 42, 42];
        $this->GuessInvalidValuesOnBoxMethod->invokeArgs($this->Object, [&$box]);
        $this->assertSame([42, 42, 42, 42], $box);
    }

    public function testBoundingBoxWithInvalidEntries(): void
    {
        $box = [3, false, 42, false];
        $this->GuessInvalidValuesOnBoxMethod->invokeArgs($this->Object, [&$box]);
        $this->assertSame([3, 22.5, 42, 22.5], $box);
    }
}
