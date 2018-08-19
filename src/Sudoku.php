<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\InvalidSectionSize;

class Sudoku
{
    private $width;
    private $height;
    private $cells;
    private $sections;

    public function __construct()
    {
        $this->width = 0;
        $this->height = 0;
        $this->cells = [];
        $this->sections = [];
    }

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

    public function createSection(array $cells): Section
    {
        $section = new Section($cells);
        $this->sections[] = new Section($cells);

        return $section;
    }

    public function getCells(): \Iterator
    {
        for ($rowIndex = 0; $rowIndex < 9; ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < 9; ++$columnIndex) {
                yield $this->cells[$rowIndex][$columnIndex];
            }
        }
    }

    public function getSections(): \Iterator
    {
        foreach ($this->sections as $section) {
            yield $section;
        }
    }

    public function __toString(): string
    {
        $string = '';
        
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
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
