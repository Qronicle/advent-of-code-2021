<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day07
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day07 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        // $this->rawInput = '16,1,2,0,4,2,7,1,2,14';
        $positions = explode(',', $this->rawInput);
        $medianPosition = $this->median($positions);
        echo "Median: $medianPosition\n";
        $target = floor($medianPosition);
        $fuel = $this->fuelAtPosition($positions, $target);
        echo "Fuel at position $target: $fuel\n";
        // Check previous positions
        $updated = false;
        while (($prevFuel = $this->fuelAtPosition($positions, $target - 1)) < $fuel) {
            $updated = true;
            $fuel = $prevFuel;
            $target--;
            echo "Fuel at previous position $target: $fuel\n";
        }
        // Check next positions
        if (!$updated) {
            while (($nextFuel = $this->fuelAtPosition($positions, $target + 1)) < $fuel) {
                $fuel = $nextFuel;
                $target++;
                echo "Fuel at next position $target: $fuel\n";
            }
        }
        return $fuel;
    }

    protected function median(array $numbers): float
    {
        sort($numbers);
        $middleIndex = floor(count($numbers) * .5);
        if ($middleIndex % 2 == 0) {
            return ($numbers[$middleIndex] + $numbers[$middleIndex + 1]) * .5;
        } else {
            return $numbers[$middleIndex];
        }
    }

    protected function fuelAtPosition(array $positions, int $target): int
    {
        $fuel = 0;
        foreach ($positions as $position) {
            $fuel += abs($target - $position);
        }
        return $fuel;
    }

    protected function solvePart2(): string
    {
        // $this->rawInput = '16,1,2,0,4,2,7,1,2,14';
        $positions = explode(',', $this->rawInput);
        $target = floor(array_sum($positions) / count($positions));
        echo "Average: $target\n";
        $fuel = $this->realFuelAtPosition($positions, $target);
        echo "Fuel at position $target: $fuel\n";
        // Check previous positions
        $updated = false;
        while (($prevFuel = $this->realFuelAtPosition($positions, $target - 1)) < $fuel) {
            $updated = true;
            $fuel = $prevFuel;
            $target--;
            echo "Fuel at previous position $target: $fuel\n";
        }
        // Check next positions
        if (!$updated) {
            while (($nextFuel = $this->realFuelAtPosition($positions, $target + 1)) < $fuel) {
                $fuel = $nextFuel;
                $target++;
                echo "Fuel at next position $target: $fuel\n";
            }
        }
        return $fuel;
    }

    protected function realFuelAtPosition(array $positions, int $target): int
    {
        $fuel = 0;
        foreach ($positions as $position) {
            $distance = abs($target - $position);
            for ($i = 1; $i <= $distance; $i++) {
                $fuel += $i;
            }
        }
        return $fuel;
    }
}
