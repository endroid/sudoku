<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Board;

use Endroid\Sudoku\Exception\InvalidBoardRepresentationException;
use Iterator;

final class Board
{
    /**
     * @var Cell[][]
     */
    private $cells;

    private $rows;
    private $columns;
    private $blocks;
    private $sections;

//    private $unpropagatedCells;
//
//    private $optionsRemoved = [];
//
//    private $moveIndex = -1;
//    private $moves = [];
//    private $storedMoves = [];
//
//    private $valid = true;

    public function __construct()
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->rows = [];
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            $this->rows[$rowIndex] = new Section($rowIndex);
        }

        $this->columns = [];
        for ($columnIndex = 0; $columnIndex < 9; $columnIndex++) {
            $this->columns[$columnIndex] = new Section($columnIndex);
        }

        $this->blocks = [];
        for ($blockIndex = 0; $blockIndex < 9; $blockIndex++) {
            $this->blocks[$blockIndex] = new Section($blockIndex);
        }

        $this->cells = [];
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            $this->cells[$rowIndex] = [];
            for ($columnIndex = 0; $columnIndex < 9; $columnIndex++) {
                $cell = new Cell();
                $cell->setPosition(
                    $this->rows[$rowIndex],
                    $this->columns[$columnIndex],
                    $this->blocks[intval(floor($rowIndex / 3) * 3 + floor($columnIndex / 3))]
                );
                $this->cells[$rowIndex][$columnIndex] = $cell;
            }
        }

        $this->sections = array_merge($this->rows, $this->columns, $this->blocks);
    }

    public static function createFromString(string $values): self
    {
        $values = preg_replace('#[^0-9]*#i', '', $values);
        $values = array_chunk(array_map('intval', str_split($values)), 9);

        return self::createFromArray($values);
    }

    public static function createFromArray(array $values): self
    {
        self::validateBoardRepresentation($values);

        $board = new self();

        foreach ($board->getCellsIterator() as $cell) {
            if ($values[$cell->getRow()->getIndex()][$cell->getColumn()->getIndex()] > 0) {
                $cell->setValue($values[$cell->getRow()->getIndex()][$cell->getColumn()->getIndex()]);
            }
        }

        return $board;
    }

    private static function validateBoardRepresentation(array $values): void
    {
        foreach ($values as $row) {
            if (count($values) !== 9 || count($row) !== 9) {
                throw new InvalidBoardRepresentationException('The input should consist of exactly 9 rows of 9 numbers in the range 0 (unknown) to 9');
            }
        }
    }

    public function setCellValue(int $rowIndex, int $columnIndex, int $value): void
    {
        $this->cells[$rowIndex][$columnIndex]->setValue($value);
    }

    /**
     * @return Cell[][]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    public function getCellsIterator(): Iterator
    {
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            for ($columnIndex = 0; $columnIndex < 9; $columnIndex++) {
                yield $this->cells[$rowIndex][$columnIndex];
            }
        }
    }

    /**
     * @return Section[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    public function toArray(): array
    {
        $values = [];
        foreach ($this->cells as $rowIndex => $row) {
            $values[$rowIndex] = [];
            foreach ($row as $columnIndex => $cell) {
                $values[$rowIndex][$columnIndex] = $cell->getValue();
            }
        }

        return $values;
    }







//    public function addOptionRemoved($cell, $option)
//    {
//        $this->storeMove();
//
//        $this->optionsRemoved[] = [$cell, $option];
//    }
//
//    public function solve($deep = true, $depth = 0)
//    {
//        // First propagate cell values
//        $this->propagateCellValues();
//
//        if (!$deep) {
//            return false;
//        }
//
//        // No more logical assignments left: start guessing
//        foreach ($this->rows as $row) {
//            foreach ($this->columns as $column) {
//                if ($this->cells[$row->getIndex()][$column->getIndex()]->value !== 0) {
//                    continue;
//                }
//                foreach ($this->cells[$row->getIndex()][$column->getIndex()]->getOptions() as $option) {
//                    $move = [$row->getIndex(), $column->getIndex(), $option];
//                    try {
//                        $this->doMove($move);
//                        $this->solve($depth + 1);
//
//                        return true;
//                    } catch (\Exception $exception) {
//                        $this->undoMove();
//                    }
//                }
//                throw new \Exception('All options tried');
//            }
//        }
//
//        if (!$this->isSolved()) {
//            $this->valid = false;
//        }
//    }
//
//    public function propagateCellValues(): void
//    {
//        while ($unpropagatedCell = array_shift($this->unpropagatedCells)) {
//            $unpropagatedCell->propagateValue();
//
//            // Propagate options removed
//            while ($optionRemoved = array_shift($this->optionsRemoved)) {
//                $optionRemoved[0]->propagateOptionRemoved($optionRemoved[1]);
//            }
//        }
//    }
//
//    public function isSolved()
//    {
//        foreach ($this->rows as $row) {
//            foreach ($this->columns as $column) {
//                if ($this->cells[$row->getIndex()][$column->getIndex()]->value === 0) {
//                    return false;
//                }
//            }
//        }
//
//        return true;
//    }
//
//    private function doMove($move)
//    {
//        $this->moveIndex = count($this->moves);
//        $this->moves[$this->moveIndex] = $move;
//        $this->setCellValue($move[0], $move[1], $move[2], false);
//    }
//
//    public function storeMove()
//    {
//        if ($this->moveIndex > -1 && !isset($this->storedMoves[$this->moveIndex])) {
//            $this->storedMoves[$this->moveIndex] = [$this->unpropagatedCells, $this->optionsRemoved];
//        }
//    }
//
//    private function undoMove()
//    {
//        if ($this->moveIndex > -1 && isset($this->storedMoves[$this->moveIndex])) {
//            $this->unpropagatedCells = $this->storedMoves[$this->moveIndex][0];
//            $this->optionsRemoved = $this->storedMoves[$this->moveIndex][1];
//            unset($this->storedMoves[$this->moveIndex]);
//        }
//
//        foreach ($this->rows as $row) {
//            foreach ($this->columns as $column) {
//                $this->cells[$row->getIndex()][$column->getIndex()]->undoMove();
//            }
//        }
//
//        foreach ($this->sections as $section) {
//            $section->undoMove();
//        }
//
//        unset($this->moves[$this->moveIndex]);
//        --$this->moveIndex;
//    }
//

//
//    public function toMultiLineString()
//    {
//        $string = $this->__toString();
//
//        return chunk_split($string, 9, "\n");
//    }
//
//    public function __toString()
//    {
//        $string = '';
//        foreach ($this->rows as $row) {
//            foreach ($this->columns as $column) {
//                $string .= $this->cells[$row->getIndex()][$column->getIndex()];
//            }
//        }
//
//        return $string;
//    }
}
