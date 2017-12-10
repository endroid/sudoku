<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

final class Move
{
    private $cell;
    private $value;

    public function __construct(Cell $cell, int $value)
    {
        $this->cell = $cell;
        $this->value = $value;
    }

    public function getCell(): Cell
    {
        return $this->cell;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
