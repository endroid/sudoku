<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Board\Board;
use Endroid\Sudoku\Board\Cell;
use Endroid\Sudoku\Exception\SudokuException;

final class Solver
{
    private $board;

    /**
     * @var State[]
     */
    private $states;

    private $propagatedCells;

    public function __construct(Board $board)
    {
        $this->board = $board;

        $this->propagatedCells = [];
    }

    public function solve(bool $allowGuessing = true): void
    {
        $this->removeOptions();

        if ($allowGuessing) {
            $this->removeOptionsByGuessing();
        }
    }

    public function removeOptions(): void
    {
        $foundCellsToPropagate = false;

        /** @var Cell $cell */
        foreach ($this->board->getCellsIterator() as $cell) {
            if (0 !== $cell->getValue() && !in_array($cell, $this->propagatedCells)) {
                $foundCellsToPropagate = true;
                foreach ($cell->getAdjacentCells() as $adjacentCell) {
                    $adjacentCell->removeOption($cell->getValue());
                }
                $this->propagatedCells[] = $cell;
            }
        }

        // Process possible new set values while propagating
        // Otherwise we end up with cells with incorrect options
        if ($foundCellsToPropagate) {
            $this->removeOptions();
        }
    }

    public function checkSectionUniques(): void
    {
        $this->removeOptions();

        $foundCellsToSetValue = false;

        foreach ($this->board->getSections() as $section) {
            /** @var Cell[][] $cellsByOption */
            $cellsByOption = [];
            foreach ($section->getCells() as $cell) {
                if (0 === $cell->getValue()) {
                    foreach ($cell->getOptions() as $option) {
                        $cellsByOption[$option][] = $cell;
                    }
                }
            }
            foreach ($cellsByOption as $option => $cells) {
                if (1 === count($cells)) {
                    $foundCellsToSetValue = true;
                    $cells[0]->setValue($option);
                }
            }
        }

        if ($foundCellsToSetValue) {
            $this->checkSectionUniques();
        }
    }

//    public function checkAvailableOptions(): void
//    {
//        foreach ($this->board->getSections() as $section) {
//            $availableValues = [];
//            $availableOptions = [];
//            foreach ($section->getCells() as $cell) {
//
//            }
//        }
//
//        $availableOptions
//
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

    public function removeOptionsByGuessing(): void
    {
        echo $this->board->toHtmlString();

        /** @var Cell $cell */
        foreach ($this->board->getCellsIterator() as $cell) {
            if (0 === $cell->getValue()) {
                foreach ($cell->getOptions() as $option) {
                    $this->states[] = State::create($this->board, $this->propagatedCells);
                    echo 'Trying option '.$option.' for cell '.$cell->getIndex().'<br />';
                    $cell->setValue($option);
                    try {
                        $this->solve(true);
                    } catch (SudokuException $exception) {
                        echo 'Failed option '.$option.' for cell '.$cell->getIndex().': '.$exception->getMessage().'<br />';
                        echo $this->board->toHtmlString();
                        $this->restorePreviousState();
                        $cell->removeOption($option);
                    }
                }

                return; // Next cell values are already defined recursively
            }
        }
    }

    public function restorePreviousState(): void
    {
        $previousState = array_pop($this->states);
        $previousState->updateBoard($this->board);
        $this->propagatedCells = $previousState->getPropagatedCells();
    }

    public function hint(): void
    {
    }
}
