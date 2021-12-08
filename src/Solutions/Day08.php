<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day08
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day08 extends AbstractSolution
{
    protected array $uniqueSegmentLengths = [2, 3, 4, 7];

    protected function solvePart1(): string
    {
        $total = 0;
        foreach ($this->getInputLines() as $line) {
            $parts = explode(' | ', $line);
            $output = explode(' ', $parts[1]);
            foreach ($output as $out) {
                if (in_array(strlen($out), $this->uniqueSegmentLengths)) {
                    $total++;
                }
            }
        }
        return $total;
    }

    protected function solvePart2(): string
    {
        $numbers = [];
        foreach ($this->getInputLines() as $line) {
            $parts = explode(' | ', $line);
            $input = explode(' ', $parts[0]);
            $input = array_map([$this, 'sortSegmentString'], $input);
            $output = explode(' ', $parts[1]);
            $output = array_map([$this, 'sortSegmentString'], $output);
            $inputPerLength = [];
            foreach ($input as $segmentString) {
                $inputPerLength[strlen($segmentString)][] = str_split($segmentString);
            }
            // 8
            $eight = reset($inputPerLength[7]);
            // one
            $one = reset($inputPerLength[2]);
            // seven
            $seven = reset($inputPerLength[3]);
            $a = array_diff($seven, $one);
            $a = reset($a);
            // four
            $four = reset($inputPerLength[4]);
            $bd = array_values(array_diff($four, $one));
            // find six (does not contain 1)
            foreach ($inputPerLength[6] as $i => $segments) {
                $oneIntersection = array_intersect($segments, $one);
                if (count($oneIntersection) < 2) {
                    $six = $segments;
                    $f = reset($oneIntersection);
                    $c = $one[0] == $f ? $one[1] : $one[0];
                    unset($inputPerLength[6][$i]);
                    break;
                }
            }
            // find 9 (contains 4)
            foreach ($inputPerLength[6] as $i => $segments) {
                $fourIntersection = array_intersect($segments, $four);
                if (count($fourIntersection) == 4) {
                    $nine = $segments;
                    $ag = array_values(array_diff($nine, $four));
                    $g = $ag[0] == $a ? $ag[1] : $ag[0];
                    unset($inputPerLength[6][$i]);
                    break;
                }
            }
            // zero
            $zero = reset($inputPerLength[6]);
            // Final conclusions
            $d = array_diff($eight, $zero);
            $d = reset($d);
            $e = array_diff($eight, $nine);
            $e = reset($e);
            $b = $bd[0] == $d ? $bd[1] : $bd[0];
            // Calculate possible output strings
            $digits = [
                $this->sortSegmentArray($zero)         => 0,
                $this->sortSegmentArray($one)          => 1,
                $this->sortSegmentString("$a$c$d$e$g") => 2,
                $this->sortSegmentString("$a$c$d$f$g") => 3,
                $this->sortSegmentArray($four)         => 4,
                $this->sortSegmentString("$a$b$d$f$g") => 5,
                $this->sortSegmentArray($six)          => 6,
                $this->sortSegmentArray($seven)        => 7,
                $this->sortSegmentArray($eight)        => 8,
                $this->sortSegmentArray($nine)         => 9,
            ];
            $number = '';
            foreach ($output as $segmentString) {
                if (!isset($digits[$segmentString])) {
                    $mapping = array_flip([
                        'a' => $a,
                        'b' => $b,
                        'c' => $c,
                        'd' => $d,
                        'e' => $e,
                        'f' => $f,
                        'g' => $g,
                    ]);
                    echo "ERROR DIGIT\n\n";
                    $this->printDigit(999, $segmentString, $mapping);
                    die;
                }
                $number .= $digits[$segmentString];
            }
            $numbers[] = (int)$number;
        }
        return array_sum($numbers);
    }

    protected function sortSegmentArray(array $segments): string
    {
        sort($segments);
        return implode('', $segments);
    }

    protected function sortSegmentString($segmentString): string
    {
        $segments = str_split($segmentString);
        sort($segments);
        return implode('', $segments);
    }

    protected function printDigit(int $digit, string $segmentString, array $mapping): void
    {
        $segments = str_split($segmentString);
        foreach ($segments as $i => $segment) {
            $segments[$i] = $mapping[$segment];
        }
        echo "$digit:\n\n";
        echo " " . (in_array('a', $segments) ? '##' : '  ') . " \n";
        echo (in_array('b', $segments) ? '#' : ' ') . '  ' . (in_array('c', $segments) ? '#' : ' ') . " \n";
        echo (in_array('b', $segments) ? '#' : ' ') . '  ' . (in_array('c', $segments) ? '#' : ' ') . " \n";
        echo " " . (in_array('d', $segments) ? '##' : ' ') . " \n";
        echo (in_array('e', $segments) ? '#' : ' ') . '  ' . (in_array('f', $segments) ? '#' : ' ') . " \n";
        echo (in_array('e', $segments) ? '#' : ' ') . '  ' . (in_array('f', $segments) ? '#' : ' ') . " \n";
        echo " " . (in_array('g', $segments) ? '##' : '  ') . " \n\n";
    }
}
