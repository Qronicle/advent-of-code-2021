<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day01
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day01 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $depths = $this->getInputLines();
        $numIncreased = 0;
        $prevDepth = array_shift($depths);
        foreach ($depths as $depth) {
            if ($depth > $prevDepth) {
                $numIncreased++;
            }
            $prevDepth = $depth;
        }
        return $numIncreased;
    }

    protected function solvePart2(): string
    {
        $depths = $this->getInputLines();
        $numIncreased = 0;
        $lastDepth = count($depths) - 4;
        for ($i = 0; $i <= $lastDepth; $i++) {
            // We can ignore the overlapping measurements
            if ($depths[$i] < $depths[$i + 3]) {
                $numIncreased++;
            }
        }
        return $numIncreased;
    }
}
