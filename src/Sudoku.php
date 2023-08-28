<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\InvalidStringRepresentationException;

final class Sudoku implements \Stringable
{
    private readonly int $base;

    /** @var array<int, array<int, Cell>> */
    private readonly array $cells;

    /** @var array<int, array<int, array<int, array<int, Cell>>>> */
    private readonly array $adjacentCells;

    public function __construct(string $values)
    {
        $values = (string) preg_replace('#[^0-9\.-]*#i', '', $values);

        $this->base = (int) sqrt(strlen($values));
        if ($this->base * $this->base !== strlen($values) || $this->base < 1) {
            throw InvalidStringRepresentationException::create($values);
        }

        $values = array_chunk(array_map('intval', str_split($values)), $this->base);

        $cells = [];
        $sections = [];
        for ($row = 0; $row < $this->base; ++$row) {
            for ($column = 0; $column < $this->base; ++$column) {
                $cells[$column][$row] = new Cell($column, $row);
                if ((int) $values[$row][$column] > 0) {
                    $cells[$column][$row]->setValue($values[$row][$column]);
                }
                $sections['row-'.$row][] = $cells[$column][$row];
                $sections['column-'.$column][] = $cells[$column][$row];
                $sections['block-'.intval(floor($row / 3) * 3 + floor($column / 3))][] = $cells[$column][$row];
            }
        }

        $this->cells = $cells;

        $adjacentCells = [];
        foreach ($sections as $sectionCells) {
            /** @var Cell $cell */
            foreach ($sectionCells as $cell) {
                /** @var Cell $adjacentCell */
                foreach ($sectionCells as $adjacentCell) {
                    if ($adjacentCell !== $cell) {
                        $adjacentCells[$cell->getX()][$cell->getY()][$adjacentCell->getX()][$adjacentCell->getY()] = $adjacentCell;
                    }
                }
            }
        }

        $this->adjacentCells = $adjacentCells;
    }

    /** @return iterable<Cell> */
    public function getAdjacentCells(Cell $cell): iterable
    {
        foreach ($this->adjacentCells[$cell->getX()][$cell->getY()] as $adjacentCells) {
            foreach ($adjacentCells as $adjacentCell) {
                yield $adjacentCell;
            }
        }
    }

    public function getCell(int $x, int $y): Cell
    {
        return $this->cells[$x][$y];
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

    public function __toString(): string
    {
        $string = '';

        for ($y = 0; $y < $this->base; ++$y) {
            for ($x = 0; $x < $this->base; ++$x) {
                if (!isset($this->cells[$x][$y])) {
                    $string .= 'x';
                } else {
                    $string .= $this->cells[$x][$y]->isFilled() ? $this->cells[$x][$y]->getValue() : '.';
                }
                $string .= ' ';
            }
            $string .= "\n";
        }

        return $string;
    }
}
