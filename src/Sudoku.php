<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\InvalidInputException;

final class Sudoku
{
    private $sections;
    private $rows;
    private $columns;
    private $blocks;

    /** @var Cell[][] */
    private $cells;
    private $unpropagatedCells;

    public $optionsRemoved = [];

    public $moveIndex = -1;
    private $moves = [];
    public $storedMoves = [];

    public $valid = true;

    public function __construct(array $values = [], bool $propagate = false)
    {
        $this->sections = [];
        $this->rows = [];
        $this->columns = [];
        $this->blocks = [];
        $this->cells = [];

        $this->unpropagatedCells = [];

        $this->createSections();
        $this->setCellValues($values, $propagate);
    }

    private function createSections()
    {
        for ($index = 0; $index < 9; ++$index) {
            $this->sections[] = $this->rows[$index] = new Row($index, $this);
            $this->sections[] = $this->columns[$index] = new Column($index, $this);
            $this->sections[] = $this->blocks[$index] = new Block($index, $this);
        }

        foreach ($this->rows as $row) {
            $this->cells[$row->getIndex()] = [];
            foreach ($this->columns as $column) {
                $this->cells[$row->getIndex()][$column->getIndex()] = new Cell($row, $column, $this->blocks[intval(floor($row->getIndex() / 3) * 3 + floor($column->getIndex() / 3))], $this);
            }
        }

        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                $this->cells[$row->getIndex()][$column->getIndex()]->setAdjacentCells();
            }
        }
    }

    private function setCellValues(array $values = [], bool $propagate = false)
    {
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                if ($values[$row->getIndex()][$column->getIndex()] > 0) {
                    $this->setCellValue($row->getIndex(), $column->getIndex(), $values[$row->getIndex()][$column->getIndex()], $propagate);
                }
            }
        }
    }

    public static function fromString(string $values): Sudoku
    {
        $values = preg_replace('#[^0-9]*#i', '', $values);

        if (!preg_match('#^[0-9]{81}$#', $values)) {
            throw new InvalidInputException('The input string should consist of exactly 81 numbers in the range 0 (unknown) to 9');
        }

        $values = array_chunk(array_map('intval', str_split($values)), 9);

        return new self($values);
    }

    public function setCellValue(int $rowIndex, int $columnIndex, int $value, bool $propagate = false): void
    {
        $this->cells[$rowIndex][$columnIndex]->setValue($value, $propagate);

        if (!$propagate) {
            $this->unpropagatedCells[$rowIndex.$columnIndex] = $this->cells[$rowIndex][$columnIndex];
        }
    }

    public function addOptionRemoved($cell, $option)
    {
        $this->storeMove();

        $this->optionsRemoved[] = [$cell, $option];
    }

    public function solve($deep = true, $depth = 0)
    {
        // First propagate cell values
        $this->propagateCellValues();

        if (!$deep) {
            return false;
        }

        // No more logical assignments left: start guessing
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                if ($this->cells[$row->getIndex()][$column->getIndex()]->value !== 0) {
                    continue;
                }
                foreach ($this->cells[$row->getIndex()][$column->getIndex()]->getOptions() as $option) {
                    $move = [$row->getIndex(), $column->getIndex(), $option];
                    try {
                        $this->doMove($move);
                        $this->solve($depth + 1);
                        return true;
                    } catch (\Exception $exception) {
                        $this->undoMove();
                    }
                }
                throw new \Exception('All options tried');
            }
        }

        if (!$this->isSolved()) {
            $this->valid = false;
        }
    }

    public function propagateCellValues(): void
    {
        while ($unpropagatedCell = array_shift($this->unpropagatedCells)) {
            $unpropagatedCell->propagateValue();

            // Propagate options removed
            while ($optionRemoved = array_shift($this->optionsRemoved)) {
                $optionRemoved[0]->propagateOptionRemoved($optionRemoved[1]);
            }
        }
    }

    public function isSolved()
    {
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                if ($this->cells[$row->getIndex()][$column->getIndex()]->value === 0) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function doMove($move)
    {
        $this->moveIndex = count($this->moves);
        $this->moves[$this->moveIndex] = $move;
        $this->setCellValue($move[0], $move[1], $move[2], false);
    }

    public function storeMove()
    {
        if ($this->moveIndex > -1 && !isset($this->storedMoves[$this->moveIndex])) {
            $this->storedMoves[$this->moveIndex] = [$this->unpropagatedCells, $this->optionsRemoved];
        }
    }

    protected function undoMove()
    {
        if ($this->moveIndex > -1 && isset($this->storedMoves[$this->moveIndex])) {
            $this->unpropagatedCells = $this->storedMoves[$this->moveIndex][0];
            $this->optionsRemoved = $this->storedMoves[$this->moveIndex][1];
            unset($this->storedMoves[$this->moveIndex]);
        }

        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                $this->cells[$row->getIndex()][$column->getIndex()]->undoMove();
            }
        }

        foreach ($this->sections as $section) {
            $section->undoMove();
        }

        unset($this->moves[$this->moveIndex]);
        --$this->moveIndex;
    }

    public function toArray()
    {
        $data = ['cells' => [], 'string' => '', 'valid' => $this->valid];
        foreach ($this->cells as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                $data['cells'][$cell->key] = $cell->toArray();
                $data['string'] .= $cell->value ?: '0';
            }
        }

        return $data;
    }

    public function toMultiLineString()
    {
        $string = $this->__toString();

        return chunk_split($string, 9, "\n");
    }

    public function __toString()
    {
        $string = '';
        foreach ($this->rows as $row) {
            foreach ($this->columns as $column) {
                $string .= $this->cells[$row->getIndex()][$column->getIndex()];
            }
        }

        return $string;
    }
}
