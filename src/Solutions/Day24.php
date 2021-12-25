<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;
use Exception;
use Throwable;

/**
 * Class Day24
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day24 extends AbstractSolution
{
    protected array $vars = [
        'l5'  => [ 1,  1,  1,  1,  1,  26,  1,  26,  26,  1, 26, 26, 26,  26],
        'l6'  => [10, 12, 10, 12, 11, -16, 10, -11, -13, 13, -8, -1, -4, -14],
        'l16' => [12,  7,  8,  8, 15,  12,  8,  13,   3, 13,  3,  9,  4,  13],
    ];

    protected function solvePart1(): string
    {
        // $this->testCustomMonad(99999999999999);
        for ($serialNumber = 99999999999999; $serialNumber >= 11111111111111; $serialNumber--) {
            $serialNumber = (string)$serialNumber;
            // skip serial numbers with zeroes
            if ($pos = strpos($serialNumber, '0')) {
                $serialNumber = substr($serialNumber, 0, $pos) . str_repeat('0', 14 - $pos);
                continue;
            }
            try {
                if ($this->monad($serialNumber)) {
                    return $serialNumber;
                }
            } catch (MonadException $ex) {
                // Skip all serial numbers starting with the current first x digits
                $serialNumber[$ex->getDigitIndex()] = $serialNumber[$ex->getDigitIndex()] - 1;
                $serialNumber++;
            }
        }
    }

    protected function solvePart2(): string
    {
        for ($serialNumber = 11111111111111; $serialNumber <= 99999999999999; $serialNumber++) {
            $serialNumber = (string)$serialNumber;
            if ($pos = strpos($serialNumber, '0')) {
                $serialNumber = substr($serialNumber, 0, $pos) . str_repeat('1', 14 - $pos);
            }
            try {
                if ($this->monad($serialNumber)) {
                    return $serialNumber;
                }
            } catch (MonadException $ex) {
                $newDigit = $serialNumber[$ex->getDigitIndex()] + 1;
                if ($newDigit < 10) {
                    $serialNumber[$ex->getDigitIndex()] = $newDigit;
                    $serialNumber = substr($serialNumber, 0, $ex->getDigitIndex() + 1)
                        . str_repeat('1', 13 - $ex->getDigitIndex());
                    $serialNumber--;
                } elseif ($ex->getDigitIndex() < 13) {
                    $serialNumber = $serialNumber + (str_repeat('8', 12 - $ex->getDigitIndex()) . '9');
                }
            }
        }
    }

    protected function monad(string $serialNumber): bool
    {
        // Check monad1 method for how we got here
        $z = 0;
        foreach (str_split($serialNumber) as $i => $char) {
            $w = (int)$char;
            $x = (($z % 26) + $this->vars['l6'][$i]) == $w ? 0 : 1;
            if ($x == 1 && $this->vars['l5'][$i] != 1) {
                throw new MonadException($serialNumber, $i);
            }
            $y1 = (25 * $x) + 1;
            $y2 = ($w + $this->vars['l16'][$i]) * $x;
            $z = floor($z / $this->vars['l5'][$i]) * $y1 + $y2;
        }
        return $z == 0;
    }

    protected function monad1(string $serialNumber): ?int
    {
        $z = 0;
        foreach (str_split($serialNumber) as $i => $char) {
            $w = (int)$char;
            $x = (($z % 26) + $this->vars['l6'][$i]) == $w ? 0 : 1; // 0 or 1, uses var6, which is negative when var5 = 26
            $y1 = (25 * $x) + 1; // 26 or 1
            $y2 = ($w + $this->vars['l16'][$i]) * $x; // only important when x = 1 => var16 is always positive => y2 >= 0
            $z = floor($z / $this->vars['l5'][$i]) * $y1 + $y2; // var5 is 1 or 26, var6 = negative when 26, var6 = positive when 1, always 7 1s and 7 26s
        }
        // 1.
        // x = v6.1 != d1
        //   = 10 != [1-9]
        //   = 1
        // z = (d1 + v16.1) * x
        //   = [13-21]
        //
        // 2.
        // x = (z % 26) + v6.2 != d2
        //   = ((d1 + v16.1) % 26) + 12 != d2
        //   = (d1 + v16.1) + 12 != d2
        //   = [13-21] + 12 != d2
        //   = [25-33] != [1-9]
        //   = 1
        // z = (z / v5.2) * ((25 * x) + 1) + ((d2 + v16.2) * x)
        //   = ([13-21] / 1) * 26 + ([1-9] + 7)
        //   = [13-21] * 26 + [8-16]
        //   = [346-562]
        //
        // 3.
        // x = (z % 26) + v6.3 != d3
        //   = ([346-562] % 26) + 10 != d3
        //   = [0-25] + 10 != d3
        //   = [10-35] != [1-9]
        //   = 1
        // z = (z / v5.3) * ((25 * x) + 1) + ((d3 + v16.3) * x)
        //   = ([346-562] / 1) * 26 + ([1-9] + 8)
        //   = [346-562] * 26 + [9-17]
        //   = big1
        //
        // 4.
        // x = (z % 26) + v6.4 != d4
        //   = [0-25] + 12 != [1-9]
        //   = [12-37] != [1-9]
        //   = 1
        // z = (z / v5.4) * ((25 * x) + 1) + ((d4 + v16.4) * x)
        //   = (big1 / 1) * 26 + ([1-9] + 8)
        //   = big2
        //
        // 5. (1 11 15)
        // x = (z % 26) + v6.5 != d5
        //   = [0-25] + 11 != [1-9]
        //   = 1
        // z = (z / v5.5) * ((25 * x) + 1) + ((d5 + v16.5) * x)
        //   = big2 * 26 + ([1-9] + 15)
        //   = big3
        //
        // 6. (26 -16 12)
        // x = (z % 26) + v6.6 != d6
        //   = [0-25] - 16 != [1-9]
        //   = [-16-9] != [1-9]
        //   = 0 || 1
        // z0= (z / v5.6) * ((25 * x) + 1) + ((d6 + v16.6) * x)
        //   = (big3 / 26) * 1 + 0
        //   = big3 / 26
        //   = big2 (?)
        // z1= (z / v5.6) * ((25 * x) + 1) + ((d6 + v16.6) * x)
        //   = (big3 / 26) * 26 + ([1-9] + 12)
        //   = big3 + [13-21]
        //   = big3.1
        //
        // => only z0 will work because we need to go down => x needs to be 0 when v5 == 26
        return $z;
    }

    protected function testCustomMonad(string $serialNumber)
    {
        $z1 = $this->monad1($serialNumber);
        $z2 = $this->process($this->getInputLines(), str_split($serialNumber));
        dd($z1, $z2, $z1 == $z2 ? ' => YES' : ' => NO');
    }

    protected function process(array $instructions, array $args): int
    {
        $memory = [];
        foreach ($instructions as $instruction) {
            $params = explode(' ', $instruction);
            $operation = array_shift($params);
            foreach ($params as $p => $param) {
                $name = $param;
                if (!is_numeric($param)) {
                    $value = $memory[$param] ?? 0;
                } else {
                    $value = $name;
                    $name = null;
                }
                $params[$p] = [$name, $value];
            }
            switch ($operation) {
                case 'inp':
                    $memory[$params[0][0]] = array_shift($args);
                    break;
                case 'add':
                    $memory[$params[0][0]] = $params[0][1] + $params[1][1];
                    break;
                case 'mul':
                    $memory[$params[0][0]] = $params[0][1] * $params[1][1];
                    break;
                case 'div':
                    $memory[$params[0][0]] = floor($params[0][1] / $params[1][1]);
                    break;
                case 'mod':
                    $memory[$params[0][0]] = $params[0][1] % $params[1][1];
                    break;
                case 'eql':
                    $memory[$params[0][0]] = $params[0][1] == $params[1][1] ? 1 : 0;
                    break;
            }
        }
        return $memory['z'];
    }
}

class MonadException extends Exception
{
    protected int $serialNumber;
    protected int $digitIndex;

    public function __construct(int $serialNumber, int $digitIndex)
    {
        $this->serialNumber = $serialNumber;
        $this->digitIndex = $digitIndex;
        parent::__construct();
    }

    public function getSerialNumber(): int
    {
        return $this->serialNumber;
    }

    public function getDigitIndex(): int
    {
        return $this->digitIndex;
    }
}
