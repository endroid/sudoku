<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\GuessUnavailableException;
use Endroid\Sudoku\Exception\NoOptionsLeftException;
use Endroid\Sudoku\Exception\SudokuException;

class Solver
{
    private $sudoku;
    private $guesses;
    private $adjacentCells;
    private $propagatedCells;

    public function __construct(Sudoku $sudoku)
    {
        $this->sudoku = $sudoku;
        $this->guesses = [];

        $this->findAdjacentCells();
    }

    public function solve(): void
    {
        $this->reduceOptions();
    }

    public function isSolved(): bool
    {
        /*
         * We only have to count the number of propagated cells and not the
         * values because invalid combinations lead to a proper exception.
         */
        return 90 === count($this->propagatedCells, COUNT_RECURSIVE);
    }

    private function reduceOptions(): void
    {
        $progress = false;

        /** @var Cell $cell */
        foreach ($this->sudoku->getCells() as $cell) {
            if ($cell->isFilled() && !isset($this->propagatedCells[$cell->getX()][$cell->getY()])) {
                /** @var Cell $adjacentCell */
                foreach ($this->getAdjacentCells($cell) as $adjacentCell) {
                    $this->removeOption($adjacentCell, (int) $cell->getValue());
                }
                $this->addPropagatedCell($cell);
                $progress = true;
            }
        }

        if ($progress) {
            $this->reduceOptions();
        } elseif (!$this->isSolved()) {
            $this->solveByGuessing();
        }
    }

    private function solveByGuessing(): void
    {
        /** @var Cell $cell */
        foreach ($this->sudoku->getCells() as $cell) {
            if (!$cell->isFilled()) {
                foreach ($cell->getOptions() as $option) {
                    $this->guess($cell, $option);
                    try {
                        $this->solve();
                    } catch (SudokuException $exception) {
                    }
                    if (!$this->isSolved()) {
                        $this->undoGuess();
                    } else {
                        return;
                    }
                }
                throw new NoOptionsLeftException();
            }
        }
    }

    private function guess(Cell $cell, int $option): void
    {
        $this->guesses[] = new Guess($cell, $option);

        $this->setValue($cell, $option);
    }

    private function undoGuess(): void
    {
        $currentGuess = $this->getCurrentGuess();

        if (!$currentGuess instanceof Guess) {
            throw GuessUnavailableException::create();
        }

        $originalOptions = $currentGuess->getOriginalOptions();

        foreach ($originalOptions as $x => $columnOptions) {
            foreach ($columnOptions as $y => $options) {
                $this->sudoku->getCell($x, $y)->setOptions($options);
            }
        }

        foreach ($currentGuess->getPropagatedCells() as $cell) {
            unset($this->propagatedCells[$cell->getX()][$cell->getY()]);
        }

        array_pop($this->guesses);
    }

    private function removeOption(Cell $cell, int $option): void
    {
        if (!$cell->hasOption($option)) {
            return;
        }

        if ($this->getCurrentGuess() instanceof Guess) {
            $this->getCurrentGuess()->setOriginalOptionsFromCell($cell);
        }

        $cell->removeOption($option);
    }

    private function setValue(Cell $cell, int $value): void
    {
        if ($this->getCurrentGuess() instanceof Guess) {
            $this->getCurrentGuess()->setOriginalOptionsFromCell($cell);
        }

        $cell->setValue($value);
    }

    private function addPropagatedCell(Cell $cell): void
    {
        if (isset($this->propagatedCells[$cell->getX()][$cell->getY()])) {
            return;
        }

        if ($this->getCurrentGuess() instanceof Guess) {
            $this->getCurrentGuess()->addPropagatedCell($cell);
        }

        $this->propagatedCells[$cell->getX()][$cell->getY()] = $cell;
    }

    private function findAdjacentCells(): void
    {
        foreach ($this->sudoku->getSections() as $section) {
            /** @var Cell $cell */
            foreach ($section->getCells() as $cell) {
                /** @var Cell $adjacentCell */
                foreach ($section->getCells() as $adjacentCell) {
                    if ($adjacentCell !== $cell) {
                        $this->adjacentCells[$cell->getX()][$cell->getY()][$adjacentCell->getX()][$adjacentCell->getY()] = $adjacentCell;
                    }
                }
            }
        }
    }

    private function getAdjacentCells(Cell $cell): \Iterator
    {
        foreach ($this->adjacentCells[$cell->getX()][$cell->getY()] as $adjacentCells) {
            foreach ($adjacentCells as $adjacentCell) {
                yield $adjacentCell;
            }
        }
    }

    private function getCurrentGuess(): ?Guess
    {
        if (0 === count($this->guesses)) {
            return null;
        }

        return end($this->guesses);
    }
}
