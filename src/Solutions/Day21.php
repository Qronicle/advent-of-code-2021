<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day21
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day21 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $players = array_map(
            fn($val) => (object)['score' => 0, 'pos' => trim(substr($val, -2)) - 1],
            $this->getInputLines()
        );
        $die = new DeterministicDie();
        $winner = null;
        while (true) {
            foreach ($players as $p => $player) {
                $roll = $die->rollMultiple(3);
                $player->pos = ($player->pos + $roll) % 10;
                $player->score += $player->pos + 1;
                if ($player->score >= 1000) {
                    $winner = $p;
                    break 2;
                }
            }
        }
        $loser = ($winner + 1) % 2;
        return $players[$loser]->score * $die->getRollCount();
    }

    protected function solvePart2(): string
    {
        $players = array_map(fn($val) => trim(substr($val, -2)) - 1, $this->getInputLines());
        $wins = [0, 0];
        $possibleRollCounts = $this->getPossibleQuantumRollCounts();
        $games[$players[0] . '.' . $players[1] . '.0.0'] = 1;
        $player = 0;
        while ($games) {
            $newGames = [];
            foreach ($games as $gameKey => $gameCount) {
                list($pos1, $pos2, $score1, $score2) = explode('.', $gameKey);
                $positions = [$pos1, $pos2];
                $scores = [$score1, $score2];
                foreach ($possibleRollCounts as $roll => $rollCount) {
                    $newPositions = $positions;
                    $newPositions[$player] = ($positions[$player] + $roll) % 10;
                    $newScores = $scores;
                    $newScores[$player] += $newPositions[$player] + 1;
                    $newGameCount = $gameCount * $rollCount;
                    if ($newScores[$player] >= 21) {
                        $wins[$player] += $newGameCount;
                    } else {
                        $newGameKey = implode('.', $newPositions) . '.' . implode('.', $newScores);
                        $newGames[$newGameKey] = isset($newGames[$newGameKey])
                            ? $newGames[$newGameKey] + $newGameCount
                            : $newGameCount;
                    }
                }
            }
            $player = ($player + 1) % 2;
            $games = $newGames;
        }
        return max($wins);
    }

    protected function getPossibleQuantumRollCounts(): array
    {
        $rollCounts = [];
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                for ($k = 1; $k <= 3; $k++) {
                    $rollTotal = $i + $j + $k;
                    $rollCounts[$rollTotal] = isset($rollCounts[$rollTotal]) ? $rollCounts[$rollTotal] + 1 : 1;
                }
            }
        }
        return $rollCounts;
    }
}

abstract class AbstractDie
{
    protected int $rollCount = 0;

    public function rollMultiple(int $amount): int
    {
        $total = 0;
        for ($i = 0; $i < $amount; $i++) {
            $total += $this->roll();
        }
        return $total;
    }

    public function roll(): int
    {
        $this->rollCount++;
        return $this->calculateRollValue();
    }

    public function getRollCount(): int
    {
        return $this->rollCount;
    }

    abstract protected function calculateRollValue(): int;
}

class DeterministicDie extends AbstractDie
{
    protected int $value = 0;

    public function calculateRollValue(): int
    {
        $this->value = ($this->value % 100) + 1;
        return $this->value;
    }
}

class NormalDie extends AbstractDie
{
    protected function calculateRollValue(): int
    {
        return rand(1, 6);
    }
}