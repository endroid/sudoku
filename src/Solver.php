<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\MoveUnavailableException;
use Endroid\Sudoku\Exception\NoOptionsLeftException;
use Endroid\Sudoku\Exception\SudokuException;

final class Solver
{
    /** @var array<Move> */
    private array $moves = [];

    /** @var array<int, array<int, Cell>> */
    private array $propagatedCells;

    public function solve(Sudoku $sudoku): void
    {
        $this->reduceOptions($sudoku);
    }

    public function isSolved(): bool
    {
        /*
         * We only have to count the number of propagated cells and not the
         * values because invalid combinations lead to a proper exception.
         */
        return 90 === count($this->propagatedCells, COUNT_RECURSIVE);
    }

    private function reduceOptions(Sudoku $sudoku): void
    {
        $progress = false;

        /** @var Cell $cell */
        foreach ($sudoku->getCells() as $cell) {
            if ($cell->isFilled() && !isset($this->propagatedCells[$cell->getX()][$cell->getY()])) {
                /** @var Cell $adjacentCell */
                foreach ($sudoku->getAdjacentCells($cell) as $adjacentCell) {
                    $this->removeOption($adjacentCell, (int) $cell->getValue());
                }
                $this->addPropagatedCell($cell);
                $progress = true;
            }
        }

        if ($progress) {
            $this->reduceOptions($sudoku);
        } elseif (!$this->isSolved()) {
            $this->solveByGuessing($sudoku);
        }
    }

    private function solveByGuessing(Sudoku $sudoku): void
    {
        /** @var Cell $cell */
        foreach ($sudoku->getCells() as $cell) {
            if (!$cell->isFilled()) {
                foreach ($cell->getOptions() as $option) {
                    $this->move($cell, $option);
                    try {
                        $this->solve($sudoku);
                    } catch (SudokuException $exception) {
                    }
                    if (!$this->isSolved()) {
                        $this->undoMove($sudoku);
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

    private function undoMove(Sudoku $sudoku): void
    {
        $currentMove = $this->getCurrentMove();

        if (!$currentMove instanceof Move) {
            throw MoveUnavailableException::create();
        }

        $originalOptions = $currentMove->getOriginalOptions();

        foreach ($originalOptions as $x => $columnOptions) {
            foreach ($columnOptions as $y => $options) {
                $sudoku->getCell($x, $y)->setOptions($options);
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

    private function getCurrentMove(): ?Move
    {
        if (0 === count($this->moves)) {
            return null;
        }

        return end($this->moves);
    }
}
