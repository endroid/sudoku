<?php

declare(strict_types=1);

namespace Endroid\Sudoku\Exception;

final class NoOptionsLeftException extends SudokuException
{
    public static function create(): self
    {
        return new self('No more options left');
    }
}
