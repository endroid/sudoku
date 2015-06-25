<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Puzzle
{
    public $debug = false;

    public $cells = array();

    public $rows = array();
    public $columns = array();
    public $blocks = array();
    public $sections = array();

    public $assignments = array();
    public $optionsRemoved = array();

    public $moveIndex = -1;
    public $moves = array();
    public $storedMoves = array();

    public function __construct($values = array())
    {
        // Create rows, columns and blocks
        for ($index = 0; $index < 9; ++$index) {
            $this->sections[] = $this->rows[$index] = new Row($index, $this);
            $this->sections[] = $this->columns[$index] = new Column($index, $this);
            $this->sections[] = $this->blocks[$index] = new Block($index, $this);
        }

        // Create cells
        foreach ($this->rows as $row) {
            $this->cells[$row->index] = array();
            foreach ($this->columns as $column) {
                $this->cells[$row->index][$column->index] = new Cell($row, $column, $this->blocks[intval(floor($row->index / 3) * 3 + floor($column->index / 3))], $this);
            }
        }

        // Set adjacent cells
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                $this->cells[$row->index][$column->index]->setAdjacentCells();
            }
        }

        // Set values if given
        $this->setValues($values);
    }

    public function setValues($values)
    {
        // Allow string input
        if (is_string($values)) {
            $values = self::toArray($values);
        }

        // Set values
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                if (intval($values[$row->index][$column->index]) > 0) {
                    $this->addAssignment($this->cells[$row->index][$column->index], $values[$row->index][$column->index]);
                }
            }
        }
    }

    public function addAssignment(Cell $cell, $value)
    {
        $this->debug('Cell '.$cell->key.' will be assigned value '.$value);

        $this->storeMove();

        $this->assignments[] = array($cell, $value);
    }

    public function addOptionRemoved($cell, $option)
    {
        $this->storeMove();

        $this->optionsRemoved[] = array($cell, $option);
    }

    public function solve($depth = 0)
    {
        while ($assignment = array_shift($this->assignments)) {
            if ($assignment[0]->value == $assignment[1]) {
                continue;
            }

            // Set value: this also removes options
            $assignment[0]->setValue($assignment[1]);

            // Propagate options removed
            while ($optionRemoved = array_shift($this->optionsRemoved)) {
                $optionRemoved[0]->propagateOptionRemoved($optionRemoved[1]);
            }
        }

        // No more logical assignments left: start guessing
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                if ($this->cells[$row->index][$column->index]->value !== null) {
                    continue;
                }
                foreach ($this->cells[$row->index][$column->index]->options as $option) {
                    $move = array($row->index, $column->index, $option);
                    try {
                        $this->doMove($move);
                        $this->solve($depth + 1);

                        return true;
                    } catch (\Exception $exception) {
                        $this->debug('Exception occurred: '.$exception->getMessage().$this);
                        $this->undoMove();
                    }
                }
                throw new \Exception('All options tried');
            }
        }

        if (!$this->isSolved()) {
            throw new \Exception('No more moves left');
        }
    }

    public function isSolved()
    {
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                if ($this->cells[$row->index][$column->index]->value === null) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function doMove($move)
    {
        $this->debug($this.'MOVE: '.$move[0].$move[1].' - '.$move[2]);
        $this->moveIndex = count($this->moves);
        $this->moves[$this->moveIndex] = $move;
        $this->addAssignment($this->cells[$move[0]][$move[1]], $move[2]);
    }

    public function storeMove()
    {
        if ($this->moveIndex > -1 && !isset($this->storedMoves[$this->moveIndex])) {
            $this->storedMoves[$this->moveIndex] = array($this->assignments, $this->optionsRemoved);
        }
    }

    protected function undoMove()
    {
        if ($this->moveIndex > -1 && isset($this->storedMoves[$this->moveIndex])) {
            $this->assignments = $this->storedMoves[$this->moveIndex][0];
            $this->optionsRemoved = $this->storedMoves[$this->moveIndex][1];
            unset($this->storedMoves[$this->moveIndex]);
        }

        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                $this->cells[$row->index][$column->index]->undoMove();
            }
        }

        foreach ($this->sections as $section) {
            $section->undoMove();
        }

        unset($this->moves[$this->moveIndex]);
        --$this->moveIndex;

        $this->debug('AFTER UNDO MOVE '.$this);
    }

    public static function toArray($values)
    {
        // Filter all but digits
        $values = preg_replace('#[^0-9]*#i', '', $values);

        // Create the rows
        $values = str_split($values, 9);

        // Create the columns
        foreach ($values as &$row) {
            $row = str_split($row);
        }

        return $values;
    }

    public function __toString()
    {
        $string = '';
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                $string .= $this->cells[$row->index][$column->index];
            }
        }

        return $string;
    }

    public function debug($message)
    {
        if ($this->debug) {
            echo $message.'<br />';
        }
    }
}
