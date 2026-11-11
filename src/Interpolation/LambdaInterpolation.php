<?php

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

class LambdaInterpolation implements InterpolationInterface
{
    use ArgumentsCheckTrait;

    /** @var \\Closure */
    protected $Lambda;

    /**
     * LambdaInterpolation constructor.
     * @param  \Closure $interpolationFunction
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    public function __construct($interpolationFunction)
    {
        if (!$this->isValidClosure($interpolationFunction)) {
            throw new \Runalyze\DEM\Exception\InvalidArgumentException('Provided closure does not fulfill the requirements.');
        }

        $this->Lambda = $interpolationFunction;
    }

    /**
     * @param  mixed $function
     * @return bool
     */
    protected function isValidClosure($function): bool
    {
        $Reflection = new \ReflectionFunction($function);

        return 3 === $Reflection->getNumberOfRequiredParameters();
    }

    /**
     * Interpolate.
     *
     * p0------------p1
     * |      |
     * |      y
     * |      |
     * |--x-- Z
     * |
     * p2------------p3
     *
     * @param  float                    $x                      x position of Z within bounding box, required: $x in [0.0, 1.0]
     * @param  float                    $y                      y position of Z within bounding box, required: $y in [0.0, 1.0]
     * @param  array<int|bool>          $elevationOnBoundingBox elevation data on [p0, p1, p2, p3]
     * @return int                      estimated elevation on point Z
     * @throws \Runalyze\DEM\Exception\InvalidArgumentException
     */
    public function interpolate(float $x, float $y, array $elevationOnBoundingBox): int
    {
        $this->checkArguments($x, $y, $elevationOnBoundingBox);

        return $this->Lambda->__invoke($x, $y, $elevationOnBoundingBox);
    }
}
