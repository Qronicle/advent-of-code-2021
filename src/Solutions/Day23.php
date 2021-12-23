<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;

/**
 * Class Day23
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day23 extends AbstractSolution
{
    protected int $roomSize;
    protected int $amphipodCount;
    protected array $rooms;
    protected array $roomTiles;
    protected array $hallwayTiles;
    protected array $moveCost = [1, 10, 100, 1000];

    protected function solvePart1(): string
    {
        $tileState = $this->init($this->getInputLines());
        return $this->run($tileState);
    }

    protected function solvePart2(): string
    {
        $tileState = $this->init($this->getInputLines(), true);
        return $this->run($tileState);
    }

    public function run(string $tileState): int
    {
        $tileStates = [$tileState => 0];
        $minTotalEnergy = null;
        while ($tileStates) {
            $tileState = key($tileStates);
            $energy = array_shift($tileStates);
            // Move all top room amphipods to hallway
            foreach ($this->rooms as $room) {
                $amphipodTileIndex = null;
                // search first tile with unfinished amphipod
                foreach ($room->tiles as $roomTileIndex) {
                    if ($tileState[$roomTileIndex] == 'X') continue 2; // this room is complete!
                    if ($tileState[$roomTileIndex] != '.') {
                        $amphipodTileIndex = $roomTileIndex;
                        break;
                    }
                }
                if (null === $amphipodTileIndex) continue; // this room is empty
                // create tile state for each hallway tile we can move to
                foreach ($this->getAvailableHallwayTiles($tileState, $amphipodTileIndex) as $hallwayTileIndex => $steps) {
                    $this->addTileState($tileState, $energy, $amphipodTileIndex, $hallwayTileIndex, $steps, $tileStates, $minTotalEnergy);
                }
            }
            // Try and move hallway amphipods to target side room
            for ($hallwayTileIndex = 0; $hallwayTileIndex < 11; $hallwayTileIndex++) {
                if ($tileState[$hallwayTileIndex] == '.') continue;
                if ($move = $this->moveAmphipodToTargetRoom($tileState, $hallwayTileIndex)) {
                    if ($totalEnergy = $this->addTileState($tileState, $energy, $hallwayTileIndex, $move[0], $move[1], $tileStates, $minTotalEnergy, true)) {
                        $minTotalEnergy = is_null($minTotalEnergy) ? $totalEnergy : min($totalEnergy, $minTotalEnergy);
                    }
                }
            }
        }
        return $minTotalEnergy;
    }

    protected function addTileState(string $tileState, int $energy, int $from, int $to, int $steps, array &$tileStates, ?int $minTotalEnergy, bool $arrived = false): ?int
    {
        $totalEnergy = $energy + ($steps * $this->moveCost[$tileState[$from]]);
        if ($minTotalEnergy && $totalEnergy > $minTotalEnergy) {
            return null;
        }
        $newTileState = $tileState;
        $newTileState[$to] = $arrived ? 'X' : $newTileState[$from];
        $newTileState[$from] = '.';
        if (isset($tileStates[$newTileState]) && $tileStates[$newTileState] < $totalEnergy) {
            return null;
        }
        if ($arrived) {
            if (substr_count($newTileState, 'X') == $this->amphipodCount) {
                return $totalEnergy;
            }
        }
        $tileStates[$newTileState] = $totalEnergy;
        return null;
    }

    protected function getAvailableHallwayTiles(string $tileState, int $roomTileIndex): array
    {
        $availableTiles = [];
        $hallwaySteps = $this->roomTiles[$roomTileIndex]->stepsToHallway;
        $hallwayTile = $this->hallwayTiles[$this->roomTiles[$roomTileIndex]->hallwayTileIndex];
        // search left
        foreach ($hallwayTile->left as $tileIndex => $steps) {
            if ($tileState[$tileIndex] != '.') break; // cannot pass through others
            $availableTiles[$tileIndex] = $hallwaySteps + $steps;
        }
        // search right
        foreach ($hallwayTile->right as $tileIndex => $steps) {
            if ($tileState[$tileIndex] != '.') break; // cannot pass through others
            $availableTiles[$tileIndex] = $hallwaySteps + $steps;
        }
        return $availableTiles;
    }

    protected function moveAmphipodToTargetRoom(string $tileState, int $hallwayTileIndex): ?array
    {
        $amphipod = $tileState[$hallwayTileIndex];
        $targetRoom = $this->rooms[$amphipod];
        $hallwayTile = $this->hallwayTiles[$hallwayTileIndex];

        // check room availability
        $roomIsFree = true;
        $bottomRoomTileIndex = null;
        foreach ($targetRoom->tiles as $roomTileIndex) {
            if ($tileState[$roomTileIndex] == '.') {
                $bottomRoomTileIndex = $roomTileIndex;
                continue;
            }
            if ($tileState[$roomTileIndex] != 'X') {
                $roomIsFree = false;
                break;
            }
        }
        if (!$roomIsFree) {
            return null;
        }

        // check hallway passage possible
        foreach ($hallwayTile->out[$amphipod]->tiles as $checkTileIndex) {
            if ($tileState[$checkTileIndex] != '.') {
                return null;
            }
        }

        $steps = $hallwayTile->out[$amphipod]->steps + $this->roomTiles[$bottomRoomTileIndex]->stepsToHallway;
        return [$bottomRoomTileIndex, $steps];
    }

    protected function parseInput(array $input, bool $unfold = false): string
    {
        if ($unfold) {
            array_splice($input, 3, 0, ['  #D#C#B#A#', '  #D#B#A#C#']);
        }
        array_pop($input);
        array_shift($input);
        $tileState = '';
        $map = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
        ];
        foreach ($input as $line) {
            foreach (str_split($line) as $char) {
                if (!in_array($char, [' ', '#'])) {
                    $tileState .= $map[$char] ?? $char;
                }
            }
        }
        return $tileState;
    }

    protected function init(array $input, bool $unfold = false): string
    {
        // Parse input
        $tileState = $this->parseInput($input, $unfold);

        // Cache the living shit out of all movement options

        $this->roomSize = (strlen($tileState) - 11) / 4;
        $this->amphipodCount = $this->roomSize * 4;
        $firstRoomIndex = 11;
        $this->rooms = [];
        for ($r = 0; $r < 4; $r++) {
            $this->rooms[$r] = (object)[
                'hallwayTileIndex' => 2 + ($r * 2),
                'tiles'            => [],
            ];
            for ($t = 0; $t < $this->roomSize; $t++) {
                $tile = $firstRoomIndex + $r + (4 * $t);
                $this->rooms[$r]->tiles[] = $tile;
                $this->roomTiles[$tile] = (object)[
                    'room'             => $r,
                    'hallwayTileIndex' => $this->rooms[$r]->hallwayTileIndex,
                    'stepsToHallway'   => $t + 1,
                ];
            }
        }
        $this->hallwayTiles = array_fill(0, 11, false);
        foreach ($this->rooms as $room) {
            $tileIndex = $room->hallwayTileIndex;
            $hallwayTile = (object)[
                'left'  => [],
                'right' => [],
                'out'   => [],
            ];
            // search left
            $steps = 0;
            for ($h = $tileIndex - 1; $h >= 0; $h--) {
                $steps++;
                if ($h % 2 == 0 && $h != 0) continue; // cannot stand in front of side room
                $hallwayTile->left[$h] = $steps;
            }
            // search right
            $steps = 0;
            for ($h = $tileIndex + 1; $h < $firstRoomIndex; $h++) {
                $steps++;
                if ($h % 2 == 0 && $h != 10) continue; // cannot stand in front of side room
                $hallwayTile->right[$h] = $steps;
            }
            $this->hallwayTiles[$tileIndex] = $hallwayTile;
        }
        // occupational tiles between any hallway tile and room
        foreach ($this->hallwayTiles as $hallwayTileIndex => $tmp) {
            if ($tmp) continue; // this is a hallway tile bordering a room
            $hallwayTile = (object)[
                'left'  => [],
                'right' => [],
                'out'   => [],
            ];
            foreach ($this->rooms as $r => $room) {
                $out = (object)[
                    'steps' => abs($room->hallwayTileIndex - $hallwayTileIndex),
                    'tiles' => [],
                ];
                if ($room->hallwayTileIndex > $hallwayTileIndex) {
                    $start = $hallwayTileIndex + 1;
                    $end = $room->hallwayTileIndex;
                } else {
                    $start = $room->hallwayTileIndex;
                    $end = $hallwayTileIndex - 1;
                }
                for ($h = $start; $h <= $end; $h++) {
                    if ($h % 2 == 0 && $h != 0 && $h != 10) continue; // cannot stand in front of side room
                    $out->tiles[] = $h;
                }
                $hallwayTile->out[$r] = $out;
            }
            $this->hallwayTiles[$hallwayTileIndex] = $hallwayTile;
        }

        // Indicate "resolved" amphipods

        foreach ($this->rooms as $amphipod => $room) {
            $checkTiles = array_reverse($room->tiles);
            for ($i = 0; $i < $this->roomSize; $i++) {
                if ($tileState[$checkTiles[$i]] != $amphipod) break;
                $tileState[$checkTiles[$i]] = 'X';
            }
        }

        return $tileState;
    }
}