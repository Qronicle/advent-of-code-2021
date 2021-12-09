<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day09
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day09 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $map = new Map($this->rawInput);
        return $map->getTotalRiskLevel();
    }

    protected function solvePart2(): string
    {
        $map = new Map($this->rawInput);
        $basinSizes = $map->getBasinSizes();
        rsort($basinSizes);
        $product = 1;
        for ($i = 0; $i < 3; $i++) {
            $product *= $basinSizes[$i];
        }
        return $product;
    }
}

class Map
{
    protected array $grid;
    protected array $directions = [[0, 1], [1, 0], [-1, 0], [0, -1]];

    public function __construct(string $input)
    {
        $rows = explode("\n", $input);
        $this->grid = [];
        foreach ($rows as $row) {
            $this->grid[] = str_split($row);
        }
    }

    public function getTotalRiskLevel(): int
    {
        $riskLevel = 0;
        foreach ($this->getLowPoints() as $lowPoint) {
            $riskLevel += 1 + $this->grid[$lowPoint[0]][$lowPoint[1]];
        }
        return $riskLevel;
    }

    public function getLowPoints(): array
    {
        $lowPoints = [];
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $depth) {
                $lowest = true;
                foreach ($this->directions as $direction) {
                    if ($depth >= ($this->grid[$y + $direction[0]][$x + $direction[1]] ?? 10)) {
                        $lowest = false;
                        break;
                    }
                }
                if ($lowest) {
                    $lowPoints[] = [$y, $x];
                }
            }
        }
        return $lowPoints;
    }

    public function getBasinSizes(): array
    {
        $sizes = [];
        foreach ($this->getLowPoints() as $lowPoint) {
            $coords = $newCoords = $allCoords = [implode('.', $lowPoint) => $lowPoint];
            $size = 0;
            while ($coords) {
                $newCoords = [];
                foreach ($coords as $coord) {
                    $depth = $this->grid[$coord[0]][$coord[1]] ?? 10;
                    if ($depth > 8) {
                        continue;
                    }
                    $size++;
                    foreach ($this->directions as $direction) {
                        $newCoord = [$coord[0] + $direction[0], $coord[1] + $direction[1]];
                        $coordKey = implode('.', $newCoord);
                        if (!isset($allCoords[$coordKey])) {
                            $newCoords[$coordKey] = $newCoord;
                            $allCoords[$coordKey] = $newCoord;
                        }
                    }
                }
                $coords = $newCoords;
            }
            $sizes[] = $size;
        }
        return $sizes;
    }
}
