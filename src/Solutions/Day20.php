<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Output\TextOutput;
use AdventOfCode\Common\Solution\AbstractSolution;
use Exception;

/**
 * Class Day20
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day20 extends AbstractSolution
{

    protected function solvePart1(): string
    {
        return $this->run(2);
    }

    protected function solvePart2(): string
    {
        return $this->run(50);
    }

    protected function run(int $steps): int
    {
        list($enhanceKey, $map) = explode("\n\n", $this->rawInput);
        $map = array_map(fn ($row) => str_split($row), explode("\n", $map));
        $zeroIsUnlit = $enhanceKey[0] == '.';
        $fullLitBecomesUnlit = $enhanceKey[511] == '.';
        if (!$zeroIsUnlit && !$fullLitBecomesUnlit) {
            throw new Exception('Unexpected image enhancement algorithm');
        }
        $enableLitToggle = !$zeroIsUnlit;
        $voidIsLit = false;
        $minX = 0;
        $maxX = count($map[0]) - 1;
        $minY = 0;
        $maxY = count($map) - 1;
        for ($i = 0; $i < $steps; $i++) {
            $out = [];
            $endX = $maxX + 1;
            $endY = $maxX + 1;
            $numLit = 0;
            $void = $enableLitToggle ? ($voidIsLit ? '#' : '.') : '.';
            for ($y = $minY - 1; $y <= $endY; $y++) {
                for ($x = $minX - 1; $x <= $endX; $x++) {
                    $bin = (($map[$y-1][$x-1] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y-1][$x] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y-1][$x+1] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y][$x-1] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y][$x] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y][$x+1] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y+1][$x-1] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y+1][$x] ?? $void) == '#' ? 1 : 0)
                         . (($map[$y+1][$x+1] ?? $void) == '#' ? 1 : 0);
                    $outPixel = $enhanceKey[base_convert($bin, 2, 10)];
                    $out[$y][$x] = $outPixel;
                    $numLit += $outPixel == '#' ? 1 : 0;
                }
            }
            $minX--;
            $maxX++;
            $minY--;
            $maxY++;
            $map = $out;
            $voidIsLit = !$voidIsLit;
        }
        return $numLit;
    }
}
