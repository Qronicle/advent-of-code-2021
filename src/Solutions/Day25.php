<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day25
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day25 extends AbstractSolution
{
    const MOVE_EAST  = '>';
    const MOVE_SOUTH = 'v';
    const EMPTY      = '.';

    protected array $grid;

    protected function solvePart1(): string
    {
        $grid = $newGrid = array_map(fn($row) => str_split($row), $this->getInputLines());
        $width = count($grid[0]);
        $height = count($grid);
        $step = 0;
        do {
            $step++;
            $numMoved = 0;
            // Move eastbound cucumbers
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    if ($grid[$y][$x] != self::MOVE_EAST) continue;
                    $nextX = ($x + 1) % $width;
                    if ($grid[$y][$nextX] != self::EMPTY) continue;
                    $newGrid[$y][$x] = self::EMPTY;
                    $newGrid[$y][$nextX] = self::MOVE_EAST;
                    $numMoved++;
                }
            }
            $grid = $newGrid;
            // Move southbound cucumbers
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    if ($grid[$y][$x] != self::MOVE_SOUTH) continue;
                    $nextY = ($y + 1) % $height;
                    if ($grid[$nextY][$x] != self::EMPTY) continue;
                    $newGrid[$y][$x] = self::EMPTY;
                    $newGrid[$nextY][$x] = self::MOVE_SOUTH;
                    $numMoved++;
                }
            }
            $grid = $newGrid;
        } while ($numMoved);
        return $step;
    }

    protected function solvePart2(): string
    {
        return 'No part 2 to be had, son';
    }
}
