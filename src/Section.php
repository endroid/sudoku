<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

final class Section
{
    public function __construct(
        /** @var array<Cell> */
        private array $cells
    ) {
    }

    /** @return iterable<Cell> */
    public function getCells(): iterable
    {
        foreach ($this->cells as $cell) {
            yield $cell;
        }
    }
}
