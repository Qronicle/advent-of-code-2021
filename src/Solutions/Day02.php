<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day02
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day02 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $directions = [
            'forward' => [1, 0],
            'down'    => [0, 1],
            'up'      => [0, -1],
        ];
        $position = [0, 0];
        foreach ($this->getInputLines() as $input) {
            list($direction, $amount) = explode(' ', $input);
            $position[0] += $directions[$direction][0] * $amount;
            $position[1] += $directions[$direction][1] * $amount;
        }
        return $position[0] * $position[1];
    }

    protected function solvePart2(): string
    {
        $pos = $depth = $aim = 0;
        foreach ($this->getInputLines() as $input) {
            list($direction, $amount) = explode(' ', $input);
            switch ($direction) {
                case 'down':
                    $aim += $amount;
                    break;
                case 'up':
                    $aim -= $amount;
                    break;
                case 'forward':
                    $pos += $amount;
                    $depth += $aim * $amount;
                    break;
            }
        }
        return $pos * $depth;
    }
}
