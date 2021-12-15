<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day15
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day15 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        return $this->run($this->getGrid());
    }

    protected function solvePart2(): string
    {
        return $this->run($this->getGrid(5));
    }

    protected function getGrid(int $scale = 1): array
    {
        $grid = array_map(fn ($row) => str_split($row), $this->getInputLines());
        if ($scale < 2) {
            return $grid;
        }
        // copy vertically
        $height = count($grid);
        for ($i = 0; $i < $scale - 1; $i++) {
            for ($y = 0; $y < $height; $y++) {
                $row = $grid[$y + ($i * $height)];
                $grid[] = array_map(fn ($val) => $val > 8 ? 1 : $val + 1, $row);
            }
        }
        // copy horizontally
        foreach ($grid as $y => $row) {
            $newRow = $row;
            for ($i = 0; $i < $scale - 1; $i++) {
                $newRow = array_map(fn ($val) => $val > 8 ? 1 : $val + 1, $newRow);
                $grid[$y] = array_merge($grid[$y], $newRow);
            }
        }
        return $grid;
    }

    protected function run(array $grid): string
    {
        $directions = [[0, 1], [1, 0], [-1, 0], [0, -1]];
        $visited = [0 => [0 => 0]];
        $newPoints = [[0, 0]];
        $finish = [count($grid) - 1, count($grid[0]) - 1];
        while (true) {
            $point = array_shift($newPoints);
            foreach ($directions as $direction) {
                $newY = $point[0] + $direction[0];
                $newX = $point[1] + $direction[1];
                if (isset($visited[$newY][$newX]) || !isset($grid[$newY][$newX])) {
                    continue;
                }
                $visited[$newY][$newX] = $totalCost = $visited[$point[0]][$point[1]] + $grid[$newY][$newX];
                if ($newY == $finish[0] && $newX == $finish[1]) {
                    return $totalCost;
                }
                // add new point to list in order of total cost
                $inserted = false;
                foreach ($newPoints as $i => $newPoint) {
                    if ($totalCost <= $visited[$newPoint[0]][$newPoint[1]]) {
                        array_splice($newPoints, $i, 0, [[$newY, $newX]]);
                        $inserted = true;
                        break;
                    }
                }
                if (!$inserted) {
                    $newPoints[] = [$newY, $newX];
                }
            }
        }
    }
}