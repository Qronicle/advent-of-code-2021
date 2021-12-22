<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day22
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day22 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $instructions = $this->getInstructions(50);
        $activeCubes = [];
        foreach ($instructions as $instruction) {
            for ($x = $instruction['x'][0]; $x <= $instruction['x'][1]; $x++) {
                for ($y = $instruction['y'][0]; $y <= $instruction['y'][1]; $y++) {
                    for ($z = $instruction['z'][0]; $z <= $instruction['z'][1]; $z++) {
                        if ($x > 50 || $y > 50 || $z > 50) continue;
                        $key = "$x.$y.$z";
                        if ($instruction['enable']) {
                            $activeCubes[$key] = true;
                        } else {
                            unset($activeCubes[$key]);
                        }
                    }
                }
            }
        }
        return count($activeCubes);
    }

    protected function solvePart2(): string
    {
        $instructions = $this->getInstructions();
        $cuboids = [];
        foreach ($instructions as $instruction) {
            $newCuboid = new Cuboid($instruction);
            $intersections = [];
            foreach ($cuboids as $cuboid) {
                if ($intersection = $newCuboid->intersection($cuboid)) {
                    $key = $intersection->toString();
                    if (isset($intersections[$key])) {
                        $intersection->value = $intersections[$key]->value;
                    }
                    $intersection->value -= $cuboid->value;
                    $intersections[$intersection->toString()] = $intersection;
                }
            }
            if ($newCuboid->enabled) {
                $cuboids[] = $newCuboid;
            }
            if ($intersections) {
                array_push($cuboids, ...array_values($intersections));
            }
        }
        $total = 0;
        foreach ($cuboids as $cuboid) {
            if ($cuboid->value > 0)
            $total += $cuboid->value * $cuboid->size;
        }
        return $total;
    }

    protected function getInstructions(int $bound = null): array
    {
        return array_filter(array_map(function ($line) use ($bound) {
            $tmp = explode(' ', $line);
            $instruction = ['enable' => $tmp[0] == 'on'];
            $tmp = explode(',', $tmp[1]);
            foreach ($tmp as $d) {
                $bounds = explode('..', substr($d, 2));
                if ($bound) {
                    $bounds[0] = max(-$bound, $bounds[0]);
                    $bounds[1] = min($bound, $bounds[1]);
                }
                if ($bounds[0] > $bounds[1]) {
                    return null;
                }
                $instruction[$d[0]] = $bounds;
            }
            return $instruction;
        }, $this->getInputLines()));
    }
}

class Cuboid
{
    public array $limits;
    public bool $enabled;

    public int $size;
    public int $value;

    protected static array $dimensions = ['x', 'y', 'z'];

    public function __construct(array $instruction)
    {
        $this->enabled = $instruction['enable'] ?? false;
        $this->value = $this->enabled ? 1 : 0;
        unset($instruction['enable']);
        $this->limits = $instruction;
        $this->size = ($this->limits['x'][1] - $this->limits['x'][0] + 1)
            * ($this->limits['y'][1] - $this->limits['y'][0] + 1)
            * ($this->limits['z'][1] - $this->limits['z'][0] + 1);
    }

    public function intersection(Cuboid $cuboid): ?Cuboid
    {
        $intersection = [];
        foreach (self::$dimensions as $d) {
            $start = max($this->limits[$d][0], $cuboid->limits[$d][0]);
            $end = min($this->limits[$d][1], $cuboid->limits[$d][1]);
            if ($end < $start) {
                return null;
            }
            $intersection[$d] = [$start, $end];
        }
        return new Cuboid($intersection);
    }

    public function toString(): string
    {
        $str = '';
        foreach ($this->limits as $d => $points) {
            $str .= $d . '=' . $points[0] . '..' . $points[1] . ($d != 'z' ? ',' : '');
        }
        return $str;
    }
}

//    _____
//          ______
//        E S

//    ______
//         _______
//         B

//    ______
//        ________
//        SE

//      ______
//    __________
//      S    E


//   --------------
//   | A          |
//   |    ------=======  C
//   -----| - - =======
//        |       |
//        | B     |
//        ---------
//
// A  = (14 * 4) *  1
// B  = ( 9 * 5) *  1
// AB = ( 9 * 2) * -1
// Total => 56 + 45 - 18 = 83

// Add C (Expected extra = 4 * 2 => 91)
// A   = (14 * 4) *  1
// B   = ( 9 * 5) *  1
// AB  = ( 9 * 2) * -1
// C   = ( 7 * 2) *  1
// AC  = ( 3 * 2) * -1
// BC  = ( 3 * 2) * -1
// ABC = ( 3 * 2) * -1
// Total => 56 + 45 - 18 + 14 - 6 - 6 - 6 = 79 (Uh oh)
//
// AC, BC & ABC are the exact same volume (X)
// We would only need to remove it once
// A  = +1  => X =   -  A = -1
// B  = +1  => X = X -  B = -2
// AB = -1  => X = X - AB = -1
//
// Resulting in:
// A  = (14 * 4) *  1
// B  = ( 9 * 5) *  1
// AB = ( 9 * 2) * -1
// C  = ( 7 * 2) *  1
// X  = ( 3 * 2) * -1
// Total => 56 + 45 - 18 + 14 - 6 = 91