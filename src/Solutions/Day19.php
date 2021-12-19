<?php

namespace AdventOfCode\Solutions;

use AdventOfCode\Common\Solution\AbstractSolution;
use Exception;

/**
 * Class Day19
 *
 * @package AdventOfCode\Solutions
 * @author  Ruud Seberechts
 */
class Day19 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        return count($this->calibrate()[0]->getDefaultDimension()->getBeacons());
    }

    protected function solvePart2(): string
    {
        $scanners = $this->calibrate();
        $count = count($scanners);
        $maxDistance = -1;
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < $count; $j++) {
                if ($i == $j) {
                    continue;
                }
                $maxDistance = max($maxDistance, $scanners[$i]->getOffset()->distanceTo($scanners[$j]->getOffset()));
            }
        }
        return $maxDistance;
    }

    /**
     * Get all scanners with their calculated offset
     *
     * Scanner 0's default dimension contains the aggregated beacon locations
     *
     * @return Scanner[]
     * @throws Exception
     */
    protected function calibrate(): array
    {
        $scans = explode("\n\n", $this->rawInput);
        // Init aggregate result dimension based on scanner 0
        $defaultScanner = (new Scanner(array_shift($scans)))->setOffset(new Vector3(0, 0, 0));
        $resultDimension = $defaultScanner->getDefaultDimension();
        // Init all other scanners & scanner dimensions
        $scanners = [];
        foreach ($scans as $scannerDescription) {
            $scanners[] = (new Scanner($scannerDescription))->dimensionize();
        }
        // Find first scanner that matches the result dimension and add it's beacons to the result dimension
        // Repeat until there are no more scanners
        $positionedScanners = [$defaultScanner];
        while ($scanners) {
            foreach ($scanners as $s => $scanner) {
                foreach ($scanner->getDimensions() as $d => $scannerDimension) {
                    if ($offset = $resultDimension->match($scannerDimension)) {
                        $resultDimension->addBeacons($scannerDimension->getBeacons(), $offset);
                        $positionedScanners[] = $scanner;
                        $scanner->setOffset($offset);
                        unset($scanners[$s]);
                        break;
                    }
                }
            }
        }
        return $positionedScanners;
    }
}

class Scanner
{
    protected int $id;

    /** @var Dimension[] */
    protected array $dimensions;

    protected Vector3 $offset;

    public function __construct(string $description)
    {
        $rows = explode("\n", $description);
        $title = explode(' ', array_shift($rows));
        $this->id = $title[2];
        $this->dimensions = [(new Dimension())->initFromList($rows)];
    }

    public function dimensionize(): self
    {
        for ($i = 1; $i < 24; $i++) {
            $this->dimensions[$i] = new Dimension();
        }
        foreach ($this->dimensions[0]->getBeacons() as $beacon) {
            foreach ($this->getAlternateDimensionBeacons($beacon) as $i => $altBeacon) {
                $this->dimensions[$i]->addBeacon($altBeacon);
            }
        }
        return $this;
    }

    /**
     * @param Vector3 $beacon
     * @return Vector3[]
     */
    protected function getAlternateDimensionBeacons(Vector3 $beacon): array
    {
        $x = $beacon->x;
        $y = $beacon->y;
        $z = $beacon->z;
        return [
            1 => new Vector3(-$z, -$y, -$x),
            new Vector3(-$z, -$x, $y),
            new Vector3(-$z, $x, -$y),
            new Vector3(-$z, $y, $x),
            new Vector3(-$y, -$z, $x),
            new Vector3(-$y, -$x, -$z),
            new Vector3(-$y, $x, $z),
            new Vector3(-$y, $z, -$x),
            new Vector3(-$x, -$z, -$y),
            new Vector3(-$x, -$y, $z),
            new Vector3(-$x, $y, -$z),
            new Vector3(-$x, $z, $y),
            new Vector3($x, -$z, $y),
            new Vector3($x, -$y, -$z),
            new Vector3($x, $z, -$y),
            new Vector3($y, -$z, -$x),
            new Vector3($y, -$x, $z),
            new Vector3($y, $x, -$z),
            new Vector3($y, $z, $x),
            new Vector3($z, -$y, $x),
            new Vector3($z, -$x, -$y),
            new Vector3($z, $x, $y),
            new Vector3($z, $y, -$x),
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDefaultDimension(): Dimension
    {
        return $this->dimensions[0];
    }

    public function getOffset(): Vector3
    {
        return $this->offset;
    }

    public function setOffset(Vector3 $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return Dimension[]
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }
}

class Dimension
{
    /** @var Vector3[] */
    protected array $beacons = [];

    public function initFromList(array $list): self
    {
        $this->beacons = [];
        foreach ($list as $point) {
            $p = explode(',', $point);
            $this->beacons[$point] = new Vector3($p[0], $p[1], $p[2]);
        }
        return $this;
    }

    public function addBeacon(Vector3 $beacon): self
    {
        // In theory we should use $beacon->serialize as key, but we know this won't matter, performance baby!
        $this->beacons[] = $beacon;
        return $this;
    }

    /**
     * @return Vector3[]
     */
    public function getBeacons(): array
    {
        return $this->beacons;
    }

    /**
     * Check whether this dimension matches with the given one (offset only)
     *
     * When at least 12 beacons in the dimension match, the offset is returned
     *
     * @param Dimension $dimension
     * @return Vector3|null
     * @throws Exception
     */
    public function match(Dimension $dimension): ?Vector3
    {
        $beaconOffsets = [];
        foreach ($this->beacons as $beacon) {
            foreach ($dimension->beacons as $otherBeacon) {
                $xOffset = $beacon->x - $otherBeacon->x;
                $yOffset = $beacon->y - $otherBeacon->y;
                $zOffset = $beacon->z - $otherBeacon->z;
                $offset = "$xOffset.$yOffset.$zOffset";
                $beaconOffsets[$offset] = isset($beaconOffsets[$offset]) ? $beaconOffsets[$offset] + 1 : 1;
            }
        }
        $matchOffsets = array_filter($beaconOffsets, fn($val) => $val > 11);
        if (!$matchOffsets) {
            return null;
        }
        if (count($matchOffsets) > 1) {
            throw new Exception('Fucked');
        }
        return Vector3::fromArray(explode('.', array_key_first($matchOffsets)));
    }

    /**
     * @param Vector3[] $beacons
     * @param Vector3   $offset
     * @return $this
     */
    public function addBeacons(array $beacons, Vector3 $offset): self
    {
        foreach ($beacons as $beacon) {
            $resultBeacon = $beacon->add($offset);
            $this->beacons[$resultBeacon->serialize()] = $resultBeacon;
        }
        return $this;
    }
}

class Vector3
{
    public int $x;
    public int $y;
    public int $z;

    public function __construct(int $x, int $y, int $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public static function fromArray(array $values): Vector3
    {
        return new Vector3($values[0], $values[1], $values[2]);
    }

    public function add(Vector3 $offset): Vector3
    {
        return new Vector3(
            $this->x + $offset->x,
            $this->y + $offset->y,
            $this->z + $offset->z
        );
    }

    public function distanceTo(Vector3 $target): int
    {
        return abs($target->x - $this->x) + abs($target->y - $this->y) + abs($target->z - $this->z);
    }

    public function serialize(): string
    {
        return $this->x . ',' . $this->y . ',' . $this->z;
    }
}
