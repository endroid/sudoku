<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\InvalidStringRepresentationException;

final readonly class Sudoku implements \Stringable
{
    private int $base;

    /** @var array<int, array<int, Cell>> */
    private array $cells;

    /** @var array<int, array<int, array<int, array<int, Cell>>>> */
    private array $adjacentCells;

    public function __construct(string $values)
    {
        $values = (string) preg_replace('#[^0-9\.-]*#i', replacement: '', subject: $values);

        $this->base = (int) sqrt(strlen($values));
        if (($this->base * $this->base) !== strlen($values) || $this->base < 1) {
            throw InvalidStringRepresentationException::create($values);
        }

        $values = array_chunk(array_map('intval', str_split($values)), $this->base);

        $cells = [];
        $sections = [];
        foreach ($values as $row => $rowValues) {
            foreach ($rowValues as $column => $value) {
                $cell = new Cell($column, $row);
                if ($value > 0) {
                    $cell->setValue($value);
                }
                $cells[$column][$row] = $cell;
                $sections['row-' . $row][] = $cell;
                $sections['column-' . $column][] = $cell;
                $sections['block-' . intval((floor($row / 3) * 3) + floor($column / 3))][] = $cell;
            }
        }

        $this->cells = $cells;
        $this->adjacentCells = self::createAdjacentCells($sections);
    }

    /**
     * @param array<string, list<Cell>> $sections
     * @return array<int, array<int, array<int, array<int, Cell>>>>
     */
    private static function createAdjacentCells(array $sections): array
    {
        $adjacentCells = [];
        foreach ($sections as $sectionCells) {
            foreach ($sectionCells as $cell) {
                foreach ($sectionCells as $adjacentCell) {
                    if ($adjacentCell === $cell) {
                        continue;
                    }

                    $adjacentCells[$cell->getX()][$cell->getY()][$adjacentCell->getX()][$adjacentCell->getY()] =
                        $adjacentCell;
                }
            }
        }

        return $adjacentCells;
    }

    /** @return iterable<Cell> */
    public function getAdjacentCells(Cell $cell): iterable
    {
        foreach ($this->adjacentCells[$cell->getX()][$cell->getY()] ?? [] as $adjacentCells) {
            foreach ($adjacentCells as $adjacentCell) {
                yield $adjacentCell;
            }
        }
    }

    public function getCell(int $x, int $y): Cell
    {
        return $this->cells[$x][$y] ?? throw new \OutOfBoundsException('Cell coordinates are outside the sudoku.');
    }

    /** @return iterable<Cell> */
    public function getCells(): iterable
    {
        foreach ($this->cells as $columnCells) {
            foreach ($columnCells as $cell) {
                yield $cell;
            }
        }
    }

    #[\Override]
    public function __toString(): string
    {
        $string = '';

        for ($y = 0; $y < $this->base; ++$y) {
            for ($x = 0; $x < $this->base; ++$x) {
                $cell = $this->cells[$x][$y] ?? null;
                $string .= null === $cell ? 'x' : $cell->getValue() ?? '.';
                $string .= ' ';
            }
            $string .= "\n";
        }

        return $string;
    }
}
