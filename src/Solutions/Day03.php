<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day03
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day03 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $reportLines = $this->getInputLines();
        // Prepare binaries array
        $binaries = array_fill(0, strlen($reportLines[0]), [0, 0]);
        // Fill binaries array
        foreach ($this->getInputLines() as $number) {
            foreach (str_split($number) as $bitNr => $bit) {
                $binaries[$bitNr][$bit]++;
            }
        }
        // Calculate gamma & epsilon rates
        $gamma = $epsilon = '';
        foreach ($binaries as $bitNr => $bitOccurrences) {
            $gammaBit = $bitOccurrences[0] > $bitOccurrences[1] ? 0 : 1;
            $gamma .= $gammaBit;
            $epsilon .= $gammaBit ? 0 : 1;
        }
        return bindec($gamma) * bindec($epsilon);
    }

    protected function solvePart2(): string
    {
        $reportLines = $this->getInputLines();
        $oxygen = $this->calculateRating($reportLines, true);
        $co2 = $this->calculateRating($reportLines, false);
        return $oxygen * $co2;
    }

    protected function calculateRating(array $potentialRatings, bool $useDominantBit = true): ?int
    {
        $binaryLength = strlen($potentialRatings[0]);
        for ($bitIndex = 0; $bitIndex < $binaryLength; $bitIndex++) {
            $wantedBit = $this->findDominantBitAtIndex($potentialRatings, $bitIndex);
            if (!$useDominantBit) {
                $wantedBit = $wantedBit ? 0 : 1;
            }
            $potentialRatings = array_filter($potentialRatings, fn($number) => $number[$bitIndex] == $wantedBit);
            if (count($potentialRatings) == 1) {
                return bindec(reset($potentialRatings));
            }
        }
        return null;
    }

    protected function findDominantBitAtIndex(array $binaryNumbers, int $index): int
    {
        $numZero = 0;
        foreach ($binaryNumbers as $binaryNumber) {
            $numZero += $binaryNumber[$index] == 0 ? 1 : 0;
        }
        $numOne = count($binaryNumbers) - $numZero;
        return $numOne >= $numZero ? 1 : 0;
    }
}
