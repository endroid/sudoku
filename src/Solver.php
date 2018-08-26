<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\NoOptionsLeftException;
use Endroid\Sudoku\Exception\SudokuException;
use PHPUnit\Exception;

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
        echo $this->sudoku;
        echo "Solve\n";
        $this->reduceOptions();
    }

    public function isSolved(): bool
    {
        /**
         * We only have to count the number of propagated cells and not the
         * values because invalid combinations lead to a proper exception.
         */
        return count($this->propagatedCells, COUNT_RECURSIVE) === 90;
    }

    private function reduceOptions(): void
    {
        echo "Reduce options\n";

        $progress = false;

        /** @var Cell $cell */
        foreach ($this->sudoku->getCells() as $cell) {
            if ($cell->isFilled() && !isset($this->propagatedCells[$cell->getX()][$cell->getY()])) {
                /** @var Cell $adjacentCell */
                echo "Propagating cell ".$cell."\n";
                foreach ($this->getAdjacentCells($cell) as $adjacentCell) {
                    $this->removeOption($adjacentCell, $cell->getValue());
                }
                $this->addPropagatedCell($cell);
                $progress = true;
            }
        }

        if ($progress) {
            $this->reduceOptions();
        } else if (!$this->isSolved()) {
            $this->findUniques();
        }
    }

    private function findUniques(): void
    {
        echo "Find uniques\n";

        $progress = false;

        foreach ($this->sudoku->getSections() as $section) {
            $cellsByOption = [];
            /** @var Cell $cell */
            foreach ($section->getCells() as $cell) {
                if (!$cell->isFilled()) {
                    foreach ($cell->getOptions() as $option) {
                        $cellsByOption[$option][] = $cell;
                    }
                }
            }
            /** @var Cell[] $cells */
            foreach ($cellsByOption as $option => $cells) {
                if (1 === count($cells)) {
                    $this->setValue($cells[0], $option);
                    $progress = true;
                }
            }
        }

        if ($progress) {
            $this->reduceOptions();
        } else if (!$this->isSolved()) {
            $this->solveByGuessing();
        }
    }

    private function solveByGuessing(): void
    {
        echo "Solve by guessing\n";

        /** @var Cell $cell */
        foreach ($this->sudoku->getCells() as $cell) {
            if (!$cell->isFilled()) {
                foreach ($cell->getOptions() as $option) {
                    $this->guess($cell, $option);
                    try {
                        $this->solve();
                    } catch (SudokuException $exception) {
                        $this->undoGuess();
                    }
                }
                throw new NoOptionsLeftException();
            }
        }
    }

    private function guess(Cell $cell, int $option): void
    {
        $this->guesses[] = new Guess($cell, $option);

        echo $this->sudoku;

        echo "Guess depth ".count($this->guesses)."\n";

        $this->setValue($cell, $option);
    }
    
    private function undoGuess(): void
    {
        echo "Undo guess depth ".count($this->guesses)."\n";

        $originalOptions = $this->getCurrentGuess()->getOriginalOptions();

        foreach ($originalOptions as $x => $columnOptions) {
            foreach ($columnOptions as $y => $options) {
                echo "Reset options ".$this->sudoku->getCell($x, $y).": ".implode(',', $options)."\n";
                $this->sudoku->getCell($x, $y)->setOptions($options);
            }
        }

        foreach ($this->getCurrentGuess()->getPropagatedCells() as $cell) {
            unset($this->propagatedCells[$cell->getX()][$cell->getY()]);
        }
        
        $invalidGuess = array_pop($this->guesses);

        $this->removeOption($invalidGuess->getCell(), $invalidGuess->getValue());
    }

    private function removeOption(Cell $cell, int $option): void
    {
        if ($this->getCurrentGuess() instanceof Guess) {
            $this->getCurrentGuess()->setOriginalOptionsFromCell($cell);
        }

        if (!$cell->hasOption($option)) {
            return;
        }

        echo 'Remove option '.$cell.': '.$option."\n";

        $cell->removeOption($option);
    }

    private function setValue(Cell $cell, int $value): void
    {
        if ($this->getCurrentGuess() instanceof Guess) {
            $this->getCurrentGuess()->setOriginalOptionsFromCell($cell);
        }

        echo 'Set value '.$cell.': '.$value."\n";

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
        if (count($this->guesses) === 0) {
            return null;
        }

        return end($this->guesses);
    }
}
