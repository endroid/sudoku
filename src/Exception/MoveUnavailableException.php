<?php

declare(strict_types=1);

namespace Endroid\Sudoku\Exception;

final class MoveUnavailableException extends SudokuException
{
    public static function create(): self
    {
        return new self('No moves available');
    }
}
