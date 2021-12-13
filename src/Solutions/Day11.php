<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;
use Exception;

/**
 * Class Day11
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day11 extends AbstractSolution
{
    protected array $grid;
    protected int $numFlashes = 0;
    protected array $directions = [[-1, 0], [-1, 1], [0, 1], [1, 1], [1, 0], [1, -1], [0, -1], [-1, -1]];

    protected function solvePart1(): string
    {
        $this->grid = array_map(fn ($line) => str_split($line), $this->getInputLines());
        for ($i = 0; $i < 100; $i++) {
            $this->tick();
        }
        return $this->numFlashes;
    }

    protected function solvePart2(): string
    {
        $this->grid = array_map(fn ($line) => str_split($line), $this->getInputLines());
        $step = 0;
        while (++$step) {
            if ($this->tick()) {
                return $step;
            }
        }
        return ':(';
    }

    protected function tick(): bool
    {
        $flashers = [];
        // Increase all by one
        foreach ($this->grid as $y => $row) {
            foreach ($this->grid as $x => $energy) {
                if (++$this->grid[$y][$x] > 9) {
                    $this->grid[$y][$x] = 0;
                    $flashers["$y$x"] = [$y, $x];
                }
            }
        }
        // Resolve flashes
        $newFlashers = $allFlashers = $flashers;
        while ($newFlashers) {
            $flashers = $newFlashers;
            $newFlashers = [];
            foreach ($flashers as $coords) {
                foreach ($this->directions as $offset) {
                    $x = $coords[1] + $offset[1];
                    $y = $coords[0] + $offset[0];
                    if (isset($allFlashers["$y$x"]) || !isset($this->grid[$y][$x])) {
                        continue;
                    }
                    if (++$this->grid[$y][$x] > 9) {
                        $this->grid[$y][$x] = 0;
                        $newFlashers[] = [$y, $x];
                        $allFlashers["$y$x"] = [$y, $x];
                    }
                }
            }
        }
        $numFlashes = count($allFlashers);
        $this->numFlashes += $numFlashes;
        return $numFlashes == 100;
    }
}
