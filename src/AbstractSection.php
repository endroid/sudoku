<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

abstract class AbstractSection
{
    private $index;
    private $cells;
    private $availableValues;
    private $sudoku;
    private $moves;

    public function __construct(int $index, Sudoku $sudoku)
    {
        $this->index = $index;
        $this->sudoku = $sudoku;
        $this->cells = [];
        $this->availableValues = array_combine(range(1, 9), range(1, 9));
        $this->moves = [];
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getCells(): array
    {
        return $this->cells;
    }

    public function addCell(Cell $cell): void
    {
        $this->cells[] = $cell;
    }





    public function valueSet($value)
    {
        if (!isset($this->availableValues[$value])) {
            throw new \Exception('Value '.$value.' not available for section '.get_class($this).' '.$this->index);
        }

        $this->storeMove();

        unset($this->availableValues[$value]);
    }

    public function checkUnique($option)
    {
        $cells = array();
        foreach ($this->cells as $cell) {
            if (in_array($option, $cell->options)) {
                $cells[] = $cell;
            }
        }

        if (count($cells) == 1 && $cells[0]->getValue() !== $option) {
            $this->sudoku->setCellValue($cells[0]->getRowIndex(), $cells[0]->getColumnIndex(), $option, false);
        }
    }

    public function checkAvailableOptions()
    {
        $options = array();
        foreach ($this->cells as $cell) {
            foreach ($cell->options as $option) {
                $options[$option] = true;
            }
        }

        if (count($options) < count($this->availableValues)) {
            throw new \Exception('Not enough options left in '.get_class($this).' '.$this->index);
        }
    }

    public function storeMove()
    {
        $moveIndex = $this->sudoku->moveIndex;
        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
            $this->moves[$moveIndex] = array($this->availableValues);
        }
    }

    public function undoMove()
    {
        $moveIndex = $this->sudoku->moveIndex;
        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
            $this->availableValues = $this->moves[$moveIndex][0];
            unset($this->moves[$moveIndex]);
        }
    }
}
