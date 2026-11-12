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

use PHPUnit\Framework\MockObject\MockObject;
use Runalyze\DEM\Exception\RuntimeException;
use Runalyze\DEM\Provider\AbstractResourceReader;

final class AbstractResourceReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  resource|bool                     $resource
     */
    protected function constructMock($resource = false): MockObject|AbstractResourceReader
    {
        return $this->getMockForAbstractClass(\Runalyze\DEM\Provider\AbstractResourceReader::class, [$resource]);
    }

    public function testConstructorAndDestructorWithoutResource(): void
    {
        $this->expectNotToPerformAssertions();

        $Reader = $this->constructMock(false);
        unset($Reader);
    }

    public function testSettingInvalidResource(): void
    {
        $this->expectException(RuntimeException::class);

        $Reader = $this->constructMock(false);
        $Reader->setResource(false);
    }

    public function testReadingThisFileForSomeFun(): void
    {
        $this->expectNotToPerformAssertions();

        $Reader = $this->constructMock(fopen(__FILE__, 'r'));
        $Reader->setResource(fopen(__FILE__, 'r'));
    }
}
