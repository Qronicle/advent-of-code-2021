<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day12
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day12 extends AbstractSolution
{
    /** @var Cave[] */
    protected array $caves;

    protected function solvePart1(): string
    {
        $this->init();
        return $this->findNumPaths();
    }

    protected function solvePart2(): string
    {
        $this->init();
        return $this->findNumPaths(true);
    }

    protected function findNumPaths(bool $allowDoubleSmall = false): int
    {
        $numPaths = 0;
        $paths = [['double' => false, 'caves' => ['start']]];
        while ($paths) {
            $newPaths = [];
            foreach ($paths as $path) {
                $cave = $this->caves[end($path['caves'])];
                foreach ($cave->connections as $caveName => $cave) {
                    if ($caveName == 'end') {
                        $numPaths++;
                        // echo 'Path found: ' . implode(', ', $path['caves']) . ", end\n";
                        continue;
                    }
                    $addable = false;
                    $double = $path['double'];
                    if ($cave->big) {
                        $addable = true;
                    } elseif ($allowDoubleSmall && !$double) {
                        if (in_array($caveName, $path['caves'])) {
                            $double = true;
                        }
                        $addable = true;
                    } elseif (!in_array($caveName, $path['caves'])) {
                        $addable = true;
                    }
                    if ($addable) {
                        $newPath = $path;
                        $newPath['double'] = $double;
                        $newPath['caves'][] = $caveName;
                        $newPaths[] = $newPath;
                    }
                }
            }
            $paths = $newPaths;
        }
        return $numPaths;
    }

    protected function init(): void
    {
        $this->caves = [];
        foreach ($this->getInputLines() as $line) {
            $connectedCaveNames = explode('-', $line);
            foreach ($connectedCaveNames as $caveName) {
                if (!isset($this->caves[$caveName])) {
                    $this->caves[$caveName] = new Cave($caveName);
                }
            }
            $this->caves[$connectedCaveNames[0]]->addConnection($this->caves[$connectedCaveNames[1]]);
            $this->caves[$connectedCaveNames[1]]->addConnection($this->caves[$connectedCaveNames[0]]);
        }
    }
}

class Cave
{
    public string $name;
    public bool $big = false;
    public array $connections = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->big = ctype_upper($name);
    }

    public function addConnection(Cave $cave)
    {
        if ($cave->name == 'start') {
            return;
        }
        $this->connections[$cave->name] = $cave;
    }

    public function toString(): string
    {
        return $this->name . ($this->big ? ' (big)' : ' (small)') . ': ' . implode(', ', array_keys($this->connections));
    }
}