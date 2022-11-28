<?php

declare(strict_types=1);

namespace Endroid\Sudoku\Exception;

final class InvalidStringRepresentationException extends SudokuException
{
    public static function create(string $values): self
    {
        return new self(sprintf('Invalid string representation: %s', $values));
    }
}
