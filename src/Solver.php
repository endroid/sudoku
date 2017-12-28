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

final class Solver
{
    private $board;
    private $propagatedCells;

    public function __construct(Board $board)
    {
        $this->board = $board;

        $this->propagatedCells = [];
    }

    public function solve(): void
    {
        echo $this->board->toHtmlString();
        $this->removeOptions();
        echo $this->board->toHtmlString();
        $this->checkSectionUniques();
        echo $this->board->toHtmlString();
    }

    public function removeOptions(): void
    {
        /** @var Cell $cell */
        foreach ($this->board->getCellsIterator() as $cell) {
            if ($cell->getValue() !== 0 && !in_array($cell, $this->propagatedCells)) {
                foreach ($cell->getAdjacentCells() as $adjacentCell) {
                    $adjacentCell->removeOption($cell->getValue());
                }
                $this->propagatedCells[] = $cell;
            }
        }
    }

    public function checkSectionUniques(): void
    {
        foreach ($this->board->getSections() as $section) {
            /** @var Cell[][] $cellsByOption */
            $cellsByOption = [];
            foreach ($section->getCells() as $cell) {
                if ($cell->getValue() === 0) {
                    foreach ($cell->getOptions() as $option) {
                        $cellsByOption[$option][] = $cell;
                    }
                }
            }
            foreach ($cellsByOption as $option => $cells) {
                if (count($cells) === 1) {
                    $cells[0]->setValue($option);
                }
            }
        }
    }

    public function guess(): void
    {

    }

    public function hint(): void
    {

    }
}
