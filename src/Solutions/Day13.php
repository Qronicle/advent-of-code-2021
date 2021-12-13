<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day13
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day13 extends AbstractSolution
{
    protected array $dots = [];
    protected array $folds = [];
    protected int $width = 0;
    protected int $height = 0;

    protected function solvePart1(): string
    {
        $this->init();
        list($dir, $pos) = $this->folds[0];
        $this->fold($dir, $pos);
        $this->renderPaper();
        $numDots = 0;
        foreach ($this->dots as $row) {
            foreach ($row as $dot) {
                $numDots++;
            }
        }
        return $numDots;
    }

    protected function solvePart2(): string
    {
        $this->init();
        foreach ($this->folds as $fold) {
            list($dir, $pos) = $fold;
            $this->fold($dir, $pos);
        }
        $this->renderPaper();
        return '';
    }

    protected function init(): void
    {
        $parts = explode("\n\n", $this->rawInput);
        $coordList = explode("\n", $parts[0]);
        $this->folds = array_map(fn($str) => explode('=', substr($str, 11)), explode("\n", $parts[1]));
        foreach ($coordList as $coordStr) {
            list($x, $y) = explode(',', $coordStr);
            $this->dots[$y][$x] = true;
            $this->width = max($this->width, $x + 1);
            $this->height = max($this->height, $y + 1);
        }
    }

    protected function fold(string $dir, int $pos): void
    {
        if ($dir == 'y') {
            for ($y = $pos + 1; $y < $this->height; $y++) {
                for ($x = 0; $x < $this->width; $x++) {
                    if (!isset($this->dots[$y][$x])) {
                        continue;
                    }
                    $this->dots[$pos * 2 - $y][$x] = true;
                    unset($this->dots[$y][$x]);
                }
            }
            $this->height = $pos;
        } elseif ($dir == 'x') {
            for ($y = 0; $y < $this->height; $y++) {
                for ($x = $pos; $x < $this->width; $x++) {
                    if (!isset($this->dots[$y][$x])) {
                        continue;
                    }
                    $this->dots[$y][$pos * 2 - $x] = true;
                    unset($this->dots[$y][$x]);
                }
            }
            $this->width = $pos;
        }
    }

    protected function renderPaper(string $title = null): void
    {
        if ($title) {
            echo $title . ":\n";
        }
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                echo ($this->dots[$y][$x] ?? false) ? '#' : ' ';
            }
            echo "\n";
        }
        echo "\n";
    }
}
