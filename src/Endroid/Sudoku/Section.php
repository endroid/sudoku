<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Section
{
    public $index;

    public $cells = array();

    public $availableValues = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9);

    public $puzzle;

    public $moves = array();

    public function __construct($index, Puzzle $puzzle)
    {
        $this->index = $index;

        $this->puzzle = $puzzle;
    }

    public function addCell(Cell $cell)
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

        if (count($cells) == 1) {
            $this->debug('detected unique value in cell '.$cells[0]->key);
            $this->puzzle->addAssignment($cells[0], $option);
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
        $moveIndex = $this->puzzle->moveIndex;
        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
            $this->moves[$moveIndex] = array($this->availableValues);
        }
    }

    public function undoMove()
    {
        $moveIndex = $this->puzzle->moveIndex;
        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
            $this->availableValues = $this->moves[$moveIndex][0];
            unset($this->moves[$moveIndex]);
        }
    }

    public function debug($message)
    {
        $this->puzzle->debug('Section '.$this->index.' '.$message);
    }
}
