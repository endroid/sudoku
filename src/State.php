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

final class State
{
    private $cellValues;
    private $cellOptions;
    private $propagatedCells;

    public function __construct()
    {
        $this->cellValues = [];
        $this->cellOptions = [];
        $this->propagatedCells = [];
    }

    public static function create(Board $board, array $propagatedCells): self
    {
        $state = new self();

        /** @var Cell $cell */
        foreach ($board->getCellsIterator() as $cell) {
            $state->cellValues[$cell->getIndex()] = $cell->getValue();
            $state->cellOptions[$cell->getIndex()] = $cell->getOptions();
        }

        $state->propagatedCells = $propagatedCells;

        return $state;
    }

    public function updateBoard(Board $board)
    {
        /** @var Cell $cell */
        foreach ($board->getCellsIterator() as $cell) {
            $cell->setState($this->cellValues[$cell->getIndex()], $this->cellOptions[$cell->getIndex()]);
        }
    }

    public function getPropagatedCells(): array
    {
        return $this->propagatedCells;
    }
}
