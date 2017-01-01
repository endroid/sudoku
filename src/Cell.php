<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Cell
{
    public $key;
    public $value = null;
    public $options = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9];

    public $adjacentCells = [];

    public $row;
    public $column;
    public $block;
    public $sections = [];

    public $puzzle;

    public $moves = [];

    public function __construct(Row $row, Column $column, Block $block, Puzzle $puzzle)
    {
        $this->key = $row->index.$column->index;

        $this->sections[] = $this->row = $row;
        $this->sections[] = $this->column = $column;
        $this->sections[] = $this->block = $block;

        $this->row->addCell($this);
        $this->column->addCell($this);
        $this->block->addCell($this);

        $this->puzzle = $puzzle;
    }

    public function setAdjacentCells()
    {
        foreach ($this->sections as $section) {
            foreach ($section->cells as $cell) {
                if ($cell != $this && !in_array($cell->key, $this->adjacentCells)) {
                    $this->adjacentCells[$cell->key] = $cell;
                }
            }
        }
    }

    public function setValue($value)
    {
        $this->debug('setting value to '.$value);

        $this->storeMove();

        $this->value = $value;

        foreach ($this->options as $option) {
            $this->removeOption($option);
        }

        foreach ($this->sections as $section) {
            $section->valueSet($value);
        }

        foreach ($this->adjacentCells as $cell) {
            $cell->removeOption($value);
        }

        $this->debug($this->puzzle);
    }

    public function removeOption($option)
    {
        if (!isset($this->options[$option])) {
            return;
        }

        $this->storeMove();

        unset($this->options[$option]);

        if ($this->value === null && count($this->options) == 0) {
            throw new \Exception('Cell '.$this->key.' has no options left');
        }

        $this->puzzle->addOptionRemoved($this, $option);
    }

    public function propagateOptionRemoved($option)
    {
        if (count($this->options) == 1) {
            $this->puzzle->addAssignment($this, end($this->options));
        }

        foreach ($this->sections as $section) {
            $section->checkUnique($option);
            $section->checkAvailableOptions();
        }
    }

    public function storeMove()
    {
        $moveIndex = $this->puzzle->moveIndex;
        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
            $this->moves[$moveIndex] = [$this->options, $this->value];
        }
    }

    public function undoMove()
    {
        $moveIndex = $this->puzzle->moveIndex;
        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
            $this->options = $this->moves[$moveIndex][0];
            $this->value = $this->moves[$moveIndex][1];
            unset($this->moves[$moveIndex]);
        }
    }

    public function __toString()
    {
        return strval($this->value);
    }

    public function debug($message)
    {
        $this->puzzle->debug('Cell '.$this->key.' '.$message);
    }
}
