<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day05
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day05 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $lines = $this->parseLines();
        $diagram = new Diagram();
        foreach ($lines as $line) {
            if (!$line->isHorizontalOrVertical()) {
                continue;
            }
            $diagram->addLine($line);
        }
        return $diagram->getNumPointsWithAtLeastXLines(2);
    }

    protected function solvePart2(): string
    {
        $lines = $this->parseLines();
        $diagram = new Diagram();
        foreach ($lines as $line) {
            $diagram->addLine($line);
        }
        return $diagram->getNumPointsWithAtLeastXLines(2);
    }

    /**
     * @return Line[]
     */
    protected function parseLines(): array
    {
        $lines = [];
        foreach ($this->getInputLines() as $line) {
            $segments = explode(' -> ', $line);
            $lines[] = new Line(explode(',', $segments[0]), explode(',', $segments[1]));
        }
        return $lines;
    }
}

class Diagram
{
    protected array $grid;
    protected int $width = 0;
    protected int $height = 0;

    public function addLine(Line $line): void
    {
        $coords = $line->getCoords();
        foreach ($coords as $point) {
            $this->grid[$point->y][$point->x] = isset($this->grid[$point->y][$point->x])
                ? $this->grid[$point->y][$point->x] + 1
                : 1;
        }
        $this->width = max($this->width, $line->start->x + 1,$line->end->x + 1);
        $this->height = max($this->height, $line->start->y + 1, $line->end->y + 1);
    }

    public function draw(): void
    {
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                echo $this->grid[$y][$x] ?? '.';
            }
            echo "\n";
        }
        echo "\n";
    }

    public function getNumPointsWithAtLeastXLines(int $atLeast): int
    {
        $count = 0;
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $numLines) {
                if ($numLines >= $atLeast) {
                    $count++;
                }
            }
        }
        return $count;
    }
}

class Line
{
    public Point $start;
    public Point $end;

    public function __construct(array $start, array $end)
    {
        $this->start = new Point($start[0], $start[1]);
        $this->end = new Point($end[0], $end[1]);
    }

    public function isHorizontalOrVertical()
    {
        return $this->start->x == $this->end->x || $this->start->y == $this->end->y;
    }

    public function toString(): string
    {
        return $this->start->x . ',' . $this->start->y . ' -> ' . $this->end->x . ',' . $this->end->y;
    }

    /**
     * @return Point[]
     */
    public function getCoords(): array
    {
        $xCoords = $this->get1DCoords($this->start->x, $this->end->x);
        $yCoords = $this->get1DCoords($this->start->y, $this->end->y);
        if (count($xCoords) != count($yCoords)) {
            if (count($xCoords) == 1) {
                $xCoords = array_fill(0, count($yCoords), $xCoords[0]);
            } else {
                $yCoords = array_fill(0, count($xCoords), $yCoords[0]);
            }
        }
        $coords = [];
        foreach ($xCoords as $i => $x) {
            $coords[] = new Point($x, $yCoords[$i]);
        }
        return $coords;
    }

    protected function get1DCoords($start, $end): array
    {
        $reverse = $start > $end;
        if ($reverse) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }
        $coords = array_keys(array_fill($start, $end - $start + 1, null));
        return $reverse ? array_reverse($coords) : $coords;
    }
}

class Point
{
    public int $x;
    public int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}