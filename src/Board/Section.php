<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Board;

final class Section
{
    private $index;

    /**
     * @var Cell[]
     */
    private $cells;

    private $availableValues;

//    private $moves;

    public function __construct(string $index)
    {
        $this->index = $index;

        $this->cells = [];
        $this->availableValues = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9
        ];

//        $this->moves = [];
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getCells(): array
    {
        return $this->cells;
    }

    public function addCell(Cell $cell): void
    {
        foreach ($this->cells as $adjacentCell) {
            $adjacentCell->addAdjacentCell($cell);
            $cell->addAdjacentCell($adjacentCell);
        }

        $this->cells[] = $cell;
    }

    /**
     * @return int[]
     */
    public function getAvailableValues(): array
    {
        return $this->availableValues;
    }














//    public function valueSet(int $value): void
//    {
//        if (!isset($this->availableValues[$value])) {
//            throw new InvalidAssignmentException('Value '.$value.' not available for section '.get_class($this).' '.$this->index);
//        }
//
//        $this->storeMove();
//
//        unset($this->availableValues[$value]);
//    }

    public function checkUnique(int $option): void
    {



        $cells = array();
        foreach ($this->cells as $cell) {
            if (in_array($option, $cell->options)) {
                $cells[] = $cell;
            }
        }

        if (1 == count($cells) && $cells[0]->getValue() !== $option) {
            $this->sudoku->setCellValue($cells[0]->getRowIndex(), $cells[0]->getColumnIndex(), $option, false);
        }
    }

//    public function checkAvailableOptions(): void
//    {
//        $options = array();
//        foreach ($this->cells as $cell) {
//            foreach ($cell->options as $option) {
//                $options[$option] = true;
//            }
//        }
//
//        if (count($options) < count($this->availableValues)) {
//            throw new \Exception('Not enough options left in '.get_class($this).' '.$this->index);
//        }
//    }
//
//    private function storeMove()
//    {
//        $moveIndex = $this->sudoku->moveIndex;
//        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
//            $this->moves[$moveIndex] = array($this->availableValues);
//        }
//    }
//
//    public function undoMove()
//    {
//        $moveIndex = $this->sudoku->moveIndex;
//        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
//            $this->availableValues = $this->moves[$moveIndex][0];
//            unset($this->moves[$moveIndex]);
//        }
//    }
}
