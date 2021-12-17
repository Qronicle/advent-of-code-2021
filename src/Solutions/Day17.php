<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day17
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day17 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $target = array_map(fn ($v) => explode('..', substr($v, 2)), explode(', ', substr($this->rawInput, 13)));
        $yv = abs($target[1][0]) - 1;
        $y = 0;
        while ($yv > 0) {
            $y += $yv--;
        }
        return $y;
    }

    protected function solvePart2(): string
    {
        $target = array_map(fn ($v) => explode('..', substr($v, 2)), explode(', ', substr($this->rawInput, 13)));
        $yv = abs($target[1][0]) - 1;
        $maxY = 0;
        while ($yv > 0) {
            $maxY += $yv--;
        }
        $minY = $target[1][0];
        $maxX = $target[0][1];
        $minX = floor($target[0][0] / 10);
        while ($this->getFinalX($minX) < $target[0][0]) {
            $minX += 1;
        }
        $hits = 0;
        for ($xv = $minX; $xv <= $maxX; $xv++) {
            for ($yv = $minY; $yv <= $maxY; $yv++) {
                if ($this->hit($target, $xv, $yv)) {
                    $hits++;
                    echo "$xv, $yv\n";
                }
            }
        }
        return $hits;
    }

    protected function getFinalX(int $xv): int
    {
        $x = 0;
        while ($xv > 0) {
            $x += $xv--;
        }
        return $x;
    }

    protected function hit(array $target, int $xv, int $yv)
    {
        $x = $y = 0;
        while ($x < $target[0][1] && $y > $target[1][0]) {
            $x += $xv;
            $y += $yv--;
            $xv = max(0, --$xv);
            if ($x >= $target[0][0] && $x <= $target[0][1] && $y >= $target[1][0] && $y <= $target[1][1]) {
                return true;
            }
        }
    }
}
