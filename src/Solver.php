<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Solver
{
    private $sudoku;

    private $adjacentCells;
    private $propagatedCells;

    public function __construct(Sudoku $sudoku)
    {
        $this->sudoku = $sudoku;
    }

    public function solve(): void
    {
        $this->findAdjacentCells();
        $this->reduceOptions();
    }

    private function reduceOptions(): void
    {
        $progress = false;

        /** @var Cell $cell */
        foreach ($this->sudoku->getCells() as $cell) {
            if ($cell->isFilled() && !isset($this->propagatedCells[$cell->getX()][$cell->getY()])) {
                /** @var Cell $adjacentCell */
                foreach ($this->getAdjacentCells($cell) as $adjacentCell) {
                    $adjacentCell->removeOption($cell->getValue());
                }
                $this->propagatedCells[$cell->getX()][$cell->getY()] = $cell;
                $progress = true;
            }
        }

        if ($progress) {
            $this->reduceOptions();
        } else {
            $this->findUniques();
        }
    }

    private function findUniques(): void
    {
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
                if (count($cells) === 1) {
                    $cells[0]->setValue($option);
                    $progress = true;
                }
            }
        }

        if ($progress) {
            $this->reduceOptions();
        }
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
}
