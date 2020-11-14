<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Move
{
    private Cell $cell;
    private int $value;

    /** @var array<int, array<int, array<int, int>>> */
    private array $originalOptions;

    /** @var array<Cell> */
    private array $propagatedCells;

    public function __construct(Cell $cell, int $value)
    {
        $this->cell = $cell;
        $this->value = $value;
        $this->originalOptions = [];
        $this->propagatedCells = [];
    }

    public function getCell(): Cell
    {
        return $this->cell;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setOriginalOptionsFromCell(Cell $cell): void
    {
        if (!isset($this->originalOptions[$cell->getX()][$cell->getY()])) {
            $this->originalOptions[$cell->getX()][$cell->getY()] = $cell->getOptions();
        }
    }

    /** @return array<int, array<int, array<int, int>>> */
    public function getOriginalOptions(): array
    {
        return $this->originalOptions;
    }

    public function addPropagatedCell(Cell $cell): void
    {
        $this->propagatedCells[] = $cell;
    }

    /** @return array<Cell> */
    public function getPropagatedCells(): array
    {
        return $this->propagatedCells;
    }
}
