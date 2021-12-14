<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day14
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day14 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        return $this->run(10);
    }

    protected function solvePart2(): string
    {
        return $this->run(40);
    }

    protected function run(int $steps): int
    {
        list($template, $inserts, $elementCounts) = $this->init();
        $pairs = array_map(fn () => 0, $inserts);
        for ($i = 1; $i < strlen($template); $i++) {
            $pairs[$template[$i - 1] . $template[$i]]++;
        }
        for ($i = 0; $i < $steps; $i++) {
            $newPairs = array_map(fn () => 0, $inserts);
            foreach ($pairs as $pair => $amount) {
                $insert = $inserts[$pair];
                $newPairs[$pair[0] . $insert] += $amount;
                $newPairs[$insert . $pair[1]] += $amount;
                $elementCounts[$insert] += $amount;
            }
            $pairs = $newPairs;
        }
        asort($elementCounts);
        return end($elementCounts) - reset($elementCounts);
    }

    protected function runSlow(int $steps): int
    {
        list($template, $inserts, $elementCounts) = $this->init();
        for ($i = 0; $i < $steps; $i++) {
            $len = strlen($template);
            $prev = $template[0];
            $polymer = $prev;
            for ($j = 1; $j < $len; $j++) {
                $curr = $template[$j];
                $insert = $inserts[$prev . $curr] ?? '';
                $polymer .= $insert . $curr;
                $elementCounts[$insert]++;
                $prev = $curr;
            }
            $template = $polymer;
        }
        asort($elementCounts);
        return end($elementCounts) - reset($elementCounts);
    }

    protected function init(): array
    {
        list($template, $insertStrs) = explode("\n\n", $this->rawInput);
        $inserts = [];
        foreach (explode("\n", $insertStrs) as $insertStr) {
            list($pair, $insert) = explode(" -> ", $insertStr);
            $inserts[$pair] = $insert;
        }
        $elementCounts = array_map(fn () => 0, array_flip($inserts));
        foreach (str_split($template) as $element) {
            $elementCounts[$element] = isset($elementCounts[$element]) ? $elementCounts[$element] + 1 : 1;
        }
        return [$template, $inserts, $elementCounts];
    }
}
