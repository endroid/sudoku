<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Board;

use Endroid\Sudoku\AbstractAction;
use Endroid\Sudoku\Exception\InvalidBoardRepresentationException;
use Iterator;
use Ramsey\Uuid\Uuid;

final class Board
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Cell[][]
     */
    private $cells;

    /**
     * @var Section[]
     */
    private $sections;

    /**
     * @var AbstractAction[]
     */
    private $actions;

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
        $this->id = Uuid::uuid4()->toString();
        $this->cells = [];
        $this->sections = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addSection(Section $section): void
    {
        $this->sections[] = $section;
        $section->addBoard($this);

        foreach ($section->getCells() as $cell) {
            $this->addCell($cell);
            $this->cells[$cell->getId()] = $cell;
        }
    }

    private function addCell(Cell $cell): void
    {
        $cell->addBoard($board);
        $this->cells[$cell->getId()] = $cell;
    }

    private function createDefaultSections(): void
    {

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








    public function toHtmlString(): string
    {
        $htmlString = '
            <style>
                .sudoku { display: table; width: 100%; }
                .row { display: table-row; }
                .cell { display: table-cell; border: 1px solid #555; padding: 10px; text-align: center; }
                .value { font-weight: bold; font-size: 2em; }
            </style>';
        $htmlString .= '<div class="sudoku">';
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            $htmlString .= '<div class="row">';
            for ($columnIndex = 0; $columnIndex < 9; $columnIndex++) {
                $blockIndex = intval(floor($rowIndex / 3) * 3 + floor($columnIndex / 3));
                $htmlString .= '<div class="cell" style="background-color: #'.$blockIndex.'f'.(9 - $blockIndex).'fff;">';
                $htmlString .= '<div class="value">'.$this->cells[$rowIndex][$columnIndex]->getValue().'</div><br />';
                $htmlString .= 'options: '.implode(',', $this->cells[$rowIndex][$columnIndex]->getOptions());
                $htmlString .= '</div>';
            }
            $htmlString .= '</div>';
        }
        $htmlString .= '</div>';
        $htmlString .= '<br /><br />';

        return $htmlString;
    }






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

}
