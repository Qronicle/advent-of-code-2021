<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day01
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day06 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        return $this->breedThemFish(80);
    }

    protected function solvePart2(): string
    {
        return $this->breedThemFish(256);
    }

    protected function breedThemFish(int $numDays): int
    {
        $fishNumbers = explode(',', $this->rawInput);
        $fish = array_fill(0, 9, 0);
        foreach ($fishNumbers as $number) {
            $fish[$number]++;
        }
        for ($day = 0; $day < $numDays; $day++) {
            $dayZeroFish = array_shift($fish);
            $fish[6] += $dayZeroFish;
            $fish[8] = $dayZeroFish;
        }
        return array_sum($fish);
    }
}
