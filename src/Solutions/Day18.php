<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day18
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day18 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $numbers = $this->getInputLines();
        $number = new SnailFishNumber(array_shift($numbers));
        while ($add = array_shift($numbers)) {
            $number->add($add);
        }
        return $number->getMagnitude();
    }

    protected function solvePart2(): string
    {
        $numbers = $this->getInputLines();
        $count = count($numbers);
        $maxMagnitude = -1;
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < $count; $j++) {
                if ($i == $j) {
                    continue;
                }
                $number = new SnailFishNumber('[' . $numbers[$i] . ',' . $numbers[$j] . ']');
                $maxMagnitude = max($maxMagnitude, $number->reduce()->getMagnitude());
            }
        }
        return $maxMagnitude;
    }
}

class SnailFishNumber
{
    protected array $numbers;
    protected array $linkedNumbers;
    protected array $structure;

    protected int $numberId = 0;
    protected bool $reduced = false;

    public function __construct(string $number)
    {
        $this->init($number);
    }

    public function init(string $number): self
    {
        $this->numbers = [];
        $this->structure = $this->initRecursive(json_decode($number));
        $this->linkedNumbers = [];
        for ($i = 0; $i < count($this->numbers); $i++) {
            $this->linkedNumbers[$i] = [
                $i ? $i - 1 : null,
                $i < count($this->numbers) - 1 ? $i + 1 : null,
            ];
        }
        $this->numberId = count($this->numbers);
        return $this;
    }

    public function add(string $number): self
    {
        $sum = '[' . $this->toString() . ',' . $number . ']';
        $this->init($sum)->reduce();
        return $this;
    }

    protected function initRecursive(array $data): array
    {
        foreach ($data as $i => $value) {
            if (is_numeric($value)) {
                $numberKey = count($this->numbers);
                $this->numbers[$numberKey] = $value;
                $data[$i] = $numberKey;
            } else {
                $data[$i] = $this->initRecursive($value);
            }
        }
        return $data;
    }

    public function reduce(): self
    {
        do {
            $updated = false;
            // Split one
            if (array_filter($this->numbers, fn ($val) => $val > 9)) {
                $this->structure = $this->split($this->structure);
                $updated = $this->reduced;
            }
            // Explode all
            do {
                $this->reduced = false;
                $this->structure = $this->explode($this->structure);
                $updated = $updated || $this->reduced;
            } while ($this->reduced);
        } while ($updated);
        return $this;
    }

    protected function split(array $data): array
    {
        if ($this->reduced) {
            return $data;
        }
        foreach ($data as $i => $leftKey) {
            if ($this->reduced) {
                break;
            }
            // Split
            if (is_numeric($leftKey)) {
                $number = $this->numbers[$leftKey];
                if ($number > 9) {
                    $left = floor($number * .5);
                    $right = $number - $left;
                    $this->numbers[$leftKey] = $left;
                    $rightKey = $this->numberId++;
                    $this->numbers[$rightKey] = $right;
                    $this->linkedNumbers[$rightKey] = [
                        $leftKey,
                        $this->linkedNumbers[$leftKey][1],
                    ];
                    $this->linkedNumbers[$leftKey][1] = $rightKey;
                    $this->linkedNumbers[$this->linkedNumbers[$rightKey][1]][0] = $rightKey;
                    $data[$i] = [$leftKey, $rightKey];
                    $this->reduced = true;
                    break;
                }
                continue;
            }
            $data[$i] = $this->split($leftKey);
        }
        return $data;
    }

    protected function explode(array $data, int $depth = 1): array
    {
        if ($this->reduced) {
            return $data;
        }
        foreach ($data as $i => $value) {
            if ($this->reduced) {
                break;
            }
            if (is_numeric($value)) {
                continue;
            }
            // Explode
            if ($depth >= 4) {
                $leftKey = $value[0];
                $rightKey = $value[1];
                if (!is_numeric($leftKey) || !is_numeric($rightKey)) {
                    $data[$i] = $this->explode($value, $depth + 1);
                    break;
                }
                // Update previous & next number values
                if (($prevKey = $this->linkedNumbers[$leftKey][0]) !== null) {
                    $this->numbers[$prevKey] += $this->numbers[$leftKey];
                }
                if (($nextKey = $this->linkedNumbers[$rightKey][1]) !== null) {
                    $this->numbers[$nextKey] += $this->numbers[$rightKey];
                }
                // Replace left with zero and remove right
                $this->numbers[$leftKey] = 0;
                $this->linkedNumbers[$leftKey][1] = $this->linkedNumbers[$rightKey][1];
                $this->linkedNumbers[$this->linkedNumbers[$leftKey][1]][0] = $leftKey;
                unset($this->numbers[$rightKey], $this->linkedNumbers[$rightKey]);
                $data[$i] = $leftKey;
                $this->reduced = true;
                break;
            }
            $data[$i] = $this->explode($value, $depth + 1);
        }
        return $data;
    }

    public function getMagnitude(array $data = null): int
    {
        $data = $data ?: $this->structure;
        $left = is_numeric($data[0]) ? $this->numbers[$data[0]] : $this->getMagnitude($data[0]);
        $right = is_numeric($data[1]) ? $this->numbers[$data[1]] : $this->getMagnitude($data[1]);
        return 3 * $left + 2 * $right;
    }

    public function toString(): string
    {
        return $this->structureToString($this->structure);
    }

    protected function structureToString(array $structure): string
    {
        return preg_replace_callback('/\\d+/', fn($val) => $this->numbers[$val[0]], json_encode($structure));
    }
}