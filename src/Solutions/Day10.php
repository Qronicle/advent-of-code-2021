<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;
use Exception;

/**
 * Class Day10
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day10 extends AbstractSolution
{
    protected array $chunkPairs = [
        '(' => ')',
        '[' => ']',
        '{' => '}',
        '<' => '>',
    ];

    protected function solvePart1(): string
    {
        $chunkScores = [
            ')' => 3,
            ']' => 57,
            '}' => 1197,
            '>' => 25137,
        ];
        $score = 0;
        foreach ($this->getInputLines() as $line) {
            try {
                $this->parse($line);
            } catch (IllegalCharacterException $ex) {
                $score += $chunkScores[$ex->getCharacter()];
            }
        }
        return $score;
    }

    protected function solvePart2(): string
    {
        $chunkScores = [
            ')' => 1,
            ']' => 2,
            '}' => 3,
            '>' => 4,
        ];
        $lineScores = [];
        foreach ($this->getInputLines() as $line) {
            try {
                $missingCharacters = $this->parse($line);
                if ($missingCharacters) {
                    $lineScore = 0;
                    foreach ($missingCharacters as $character) {
                        $lineScore = ($lineScore * 5) + $chunkScores[$character];
                    }
                    $lineScores[] = $lineScore;
                }
            } catch (IllegalCharacterException $ex) {
                // We just ignore these babies
            }
        }
        sort($lineScores);
        return $lineScores[floor(count($lineScores) * .5)];
    }

    protected function parse(string $line): array
    {
        $strlen = strlen($line);
        $chunkStack = [];
        for ($i = 0; $i < $strlen; $i++) {
            $char = $line[$i];
            if ($endChar = ($this->chunkPairs[$char] ?? false)) {
                $chunkStack[] = $endChar;
            } else {
                if ($char != end($chunkStack)) {
                    throw new IllegalCharacterException($char);
                }
                array_pop($chunkStack);
            }
        }
        return array_reverse($chunkStack);
    }
}

class IllegalCharacterException extends Exception
{
    protected string $character;

    public function __construct(string $character)
    {
        $this->character = $character;
        parent::__construct();
    }

    public function getCharacter(): string
    {
        return $this->character;
    }
}