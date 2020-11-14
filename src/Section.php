<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Section
{
    /** @var array<Cell> */
    private array $cells;

    /** @param array<Cell> $cells */
    public function __construct(array $cells)
    {
        $this->cells = $cells;
    }

    /** @return \Iterator<Cell> */
    public function getCells(): \Iterator
    {
        foreach ($this->cells as $cell) {
            yield $cell;
        }
    }
}
