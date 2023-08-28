<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

final class Move
{
    /** @var array<int, array<int, array<int, int>>> */
    private array $originalOptions = [];

    /** @var array<Cell> */
    private array $propagatedCells = [];

    public function __construct(
        private readonly Cell $cell,
        private readonly int $value
    ) {
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
