<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Exception;

class InvalidStringRepresentationException extends SudokuException
{
    public static function create(string $values): self
    {
        return new self('Invalid string representation: %s');
    }
}
