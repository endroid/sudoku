<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

final class Sudoku implements \Stringable
{
    /** @var array<int, array<int, Cell>> */
    private array $cells = [];

    /** @var array<Section> */
    private array $sections = [];

    private int $width = 0;
    private int $height = 0;

    public function createCell(int $x, int $y): Cell
    {
        if (!isset($this->cells[$x][$y])) {
            $cell = new Cell($x, $y);
            $this->cells[$x][$y] = $cell;
        } else {
            $cell = $this->cells[$x][$y];
        }

        $this->width = max($this->width, $x + 1);
        $this->height = max($this->height, $y + 1);

        return $cell;
    }

    /** @param array<Cell> $cells */
    public function createSection(array $cells): Section
    {
        $section = new Section($cells);
        $this->sections[] = new Section($cells);

        return $section;
    }

    public function getCell(int $x, int $y): Cell
    {
        return $this->cells[$x][$y];
    }

    /** @return iterable<Cell> */
    public function getCells(): iterable
    {
        foreach ($this->cells as $x => $columnCells) {
            foreach ($columnCells as $y => $cell) {
                yield $cell;
            }
        }
    }

    /** @return iterable<Section> */
    public function getSections(): iterable
    {
        foreach ($this->sections as $section) {
            yield $section;
        }
    }

    public function __toString(): string
    {
        $string = '';

        for ($y = 0; $y < $this->height; ++$y) {
            for ($x = 0; $x < $this->width; ++$x) {
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
