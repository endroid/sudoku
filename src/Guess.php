<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Guess
{
    private $cell;
    private $value;
    private $originalOptions;
    private $propagatedCells;

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

    public function getOriginalOptions(): array
    {
        return $this->originalOptions;
    }

    public function addPropagatedCell(Cell $cell): void
    {
        $this->propagatedCells[] = $cell;
    }

    public function getPropagatedCells(): array
    {
        return $this->propagatedCells;
    }
}
