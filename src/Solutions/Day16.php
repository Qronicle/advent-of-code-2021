<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day16
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day16 extends AbstractSolution
{
    const PACKAGE_TYPE_SUM     = 0;
    const PACKAGE_TYPE_PRODUCT = 1;
    const PACKAGE_TYPE_MIN     = 2;
    const PACKAGE_TYPE_MAX     = 3;
    const PACKAGE_TYPE_LITERAL = 4;
    const PACKAGE_TYPE_GREATER = 5;
    const PACKAGE_TYPE_LESS    = 6;
    const PACKAGE_TYPE_EQUAL   = 7;

    protected array $versions = [];

    protected function solvePart1(): string
    {
        $this->resolve($this->hex2bin($this->rawInput));
        return array_sum($this->versions);
    }

    protected function solvePart2(): string
    {
        return $this->resolve($this->hex2bin($this->rawInput));;
    }

    protected function resolve(string $bin, int &$pos = 0): int
    {
        $version = base_convert(substr($bin, $pos, 3), 2, 10);
        $this->versions[] = $version;
        $type = base_convert(substr($bin, $pos + 3, 3), 2, 10);
        $pos += 6;
        // Handle literals
        if ($type == self::PACKAGE_TYPE_LITERAL) {
            return $this->resolveLiteral($bin, $pos);
        }
        // Handle operators
        $args = $this->resolveSubPackageArguments($bin, $pos);
        switch ($type) {
            case self::PACKAGE_TYPE_SUM:
                return array_sum($args);
            case self::PACKAGE_TYPE_PRODUCT:
                return array_product($args);
            case self::PACKAGE_TYPE_MIN:
                return min($args);
            case self::PACKAGE_TYPE_MAX:
                return max($args);
            case self::PACKAGE_TYPE_GREATER:
                return $args[0] > $args[1] ? 1 : 0;
            case self::PACKAGE_TYPE_LESS:
                return $args[0] < $args[1] ? 1 : 0;
            case self::PACKAGE_TYPE_EQUAL:
                return $args[0] == $args[1] ? 1 : 0;
        }
        throw new \Exception('Unexpected package type: ' . $type);
    }

    protected function resolveLiteral(string $bin, int &$pos): int
    {
        // read 5 bits until first is zero
        $literal = '';
        do {
            $literal .= substr($bin, $pos + 1, 4);
            $pos += 5;
        } while ($bin[$pos - 5] !== '0');
        return base_convert($literal, 2, 10);
    }

    protected function resolveSubPackageArguments(string $bin, int &$pos): array
    {
        $typeId = $bin[$pos++];
        $args = [];
        switch ($typeId) {
            case 0: // total length in bits
                $length = base_convert(substr($bin, $pos, 15), 2, 10);
                $pos += 15;
                $end = $pos + $length;
                while ($pos < $end) {
                    $args[] = $this->resolve($bin, $pos);
                }
                return $args;
            case 1: // number of sub-packets immediately contained
                $count = base_convert(substr($bin, $pos, 11), 2, 10);
                $pos += 11;
                for ($i = 0; $i < $count; $i++) {
                    $args[] = $this->resolve($bin, $pos);
                }
                return $args;
        }
    }

    /**
     * Custom hexadecimal to binary string function, because base_convert fails on large numbers
     *
     * @param string $hex
     * @return string
     */
    protected function hex2bin(string $hex): string
    {
        $table = [
            '0000', '0001', '0010', '0011', '0100', '0101', '0110', '0111', '1000', '1001',
            'A' => '1010', 'B' => '1011', 'C' => '1100', 'D' => '1101', 'E' => '1110', 'F' => '1111',
        ];
        $bin = '';
        for($i = 0; $i < strlen($hex); $i++) {
            $bin .= $table[$hex[$i]];
        }
        return $bin;
    }
}
