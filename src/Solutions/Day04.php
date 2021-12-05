<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day04
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day04 extends AbstractSolution
{
    protected array $boards = [];

    protected function solvePart1(): string
    {
        $draws = $this->parseInput();
        foreach ($draws as $number) {
            if ($winningBoards = $this->bingo($number)) {
                return $number * reset($winningBoards)->getUnmarkedSum();
            }
        }
        return ':(';
    }

    protected function solvePart2(): string
    {
        $draws = $this->parseInput();
        foreach ($draws as $number) {
            if ($winningBoards = $this->bingo($number)) {
                foreach ($winningBoards as $board) {
                    if (count($this->boards) == 1) {
                        return $number * $board->getUnmarkedSum();
                    }
                    unset($this->boards[$board->getId()]);
                }
            }
        }
        return ':(';
    }

    protected function parseInput(): array
    {
        $boardsInput = explode("\n\n", $this->rawInput);
        $draws = explode(',', array_shift($boardsInput));
        foreach ($boardsInput as $id => $boardInput) {
            $this->boards[$id] = new Board($id, $boardInput);
        }
        return $draws;
    }

    /**
     * @param int $number
     * @return Board[]
     */
    protected function bingo(int $number): array
    {
        $winningBoards = [];
        foreach ($this->boards as $i => $board) {
            $won = $board->mark($number);
            if ($won) {
                $winningBoards[] = $board;
            }
        }
        return $winningBoards;
    }
}

class Board
{
    const SIZE = 5;

    protected int $id;

    protected array $colCount;
    protected array $rowCount;

    /** @var BingoNumber[] */
    protected array $numbers;

    public function __construct(int $id, string $input)
    {
        $this->id = $id;
        $this->colCount = $this->rowCount = array_fill(0, self::SIZE, 0);
        $rows = explode("\n", $input);
        $this->numbers = [];
        foreach ($rows as $rowIndex => $row) {
            $cols = explode(' ', $row);
            $numbers = array_values(array_filter($cols, fn(string $value) => $value !== ''));
            foreach ($numbers as $colIndex => $number) {
                $this->numbers[$number] = new BingoNumber($rowIndex, $colIndex);
            }
        }
    }

    public function mark(int $number): bool
    {
        if (!isset($this->numbers[$number])) {
            return false;
        }
        $bingoNumber = $this->numbers[$number];
        $bingoNumber->marked = true;
        $this->colCount[$bingoNumber->col]++;
        $this->rowCount[$bingoNumber->row]++;
        return $this->colCount[$bingoNumber->col] == self::SIZE || $this->rowCount[$bingoNumber->row] == self::SIZE;
    }

    public function getUnmarkedSum(): int
    {
        $total = 0;
        foreach ($this->numbers as $number => $bingoNumber) {
            if (!$bingoNumber->marked) {
                $total += $number;
            }
        }
        return $total;
    }

    public function getId(): int
    {
        return $this->id;
    }
}

class BingoNumber
{
    public int $row;
    public int $col;
    public bool $marked = false;

    public function __construct(int $row, int $col)
    {
        $this->row = $row;
        $this->col = $col;
    }
}
