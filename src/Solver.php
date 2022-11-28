<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\MoveUnavailableException;
use Endroid\Sudoku\Exception\NoOptionsLeftException;
use Endroid\Sudoku\Exception\SudokuException;

class Solver
{
    /** @var array<Move> */
    private array $moves = [];

    /** @var array<int, array<int, array<int, array<int, Cell>>>> */
    private array $adjacentCells;

    /** @var array<int, array<int, Cell>> */
    private array $propagatedCells;

    public function __construct(
        private Sudoku $sudoku
    ) {
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
                    $this->move($cell, $option);
                    try {
                        $this->solve();
                    } catch (SudokuException $exception) {
                    }
                    if (!$this->isSolved()) {
                        $this->undoMove();
                    } else {
                        return;
                    }
                }
                throw new NoOptionsLeftException();
            }
        }
    }

    private function move(Cell $cell, int $option): void
    {
        $this->moves[] = new Move($cell, $option);

        $this->setValue($cell, $option);
    }

    private function undoMove(): void
    {
        $currentMove = $this->getCurrentMove();

        if (!$currentMove instanceof Move) {
            throw MoveUnavailableException::create();
        }

        $originalOptions = $currentMove->getOriginalOptions();

        foreach ($originalOptions as $x => $columnOptions) {
            foreach ($columnOptions as $y => $options) {
                $this->sudoku->getCell($x, $y)->setOptions($options);
            }
        }

        foreach ($currentMove->getPropagatedCells() as $cell) {
            unset($this->propagatedCells[$cell->getX()][$cell->getY()]);
        }

        array_pop($this->moves);
    }

    private function removeOption(Cell $cell, int $option): void
    {
        if (!$cell->hasOption($option)) {
            return;
        }

        $currentMove = $this->getCurrentMove();

        if ($currentMove instanceof Move) {
            $currentMove->setOriginalOptionsFromCell($cell);
        }

        $cell->removeOption($option);
    }

    private function setValue(Cell $cell, int $value): void
    {
        $currentMove = $this->getCurrentMove();

        if ($currentMove instanceof Move) {
            $currentMove->setOriginalOptionsFromCell($cell);
        }

        $cell->setValue($value);
    }

    private function addPropagatedCell(Cell $cell): void
    {
        if (isset($this->propagatedCells[$cell->getX()][$cell->getY()])) {
            return;
        }

        $currentMove = $this->getCurrentMove();

        if ($currentMove instanceof Move) {
            $currentMove->addPropagatedCell($cell);
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

    /** @return iterable<Cell> */
    private function getAdjacentCells(Cell $cell): iterable
    {
        foreach ($this->adjacentCells[$cell->getX()][$cell->getY()] as $adjacentCells) {
            foreach ($adjacentCells as $adjacentCell) {
                yield $adjacentCell;
            }
        }
    }

    private function getCurrentMove(): ?Move
    {
        if (0 === count($this->moves)) {
            return null;
        }

        return end($this->moves);
    }
}
