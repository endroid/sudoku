<?php

declare(strict_types=1);

namespace Endroid\Sudoku\Tests;

use Endroid\Sudoku\Puzzle;
use Endroid\Sudoku\Solver;
use Endroid\Sudoku\Sudoku;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class SolverTest extends TestCase
{
    #[TestDox('Solving sudoku "$name"')]
    #[DataProvider('sudokuProvider')]
    public function testSolver(string $name, string $puzzle): void
    {
        set_time_limit(60);

        $solver = new Solver();
        $puzzle = new Sudoku($puzzle);
        $solver->solve($puzzle);

        $this->assertTrue($solver->isSolved());
    }

    public function testCellOutsidePuzzleIsRejected(): void
    {
        $sudoku = new Sudoku(Puzzle::NoGuessing->value);
        self::assertSame(8, $sudoku->getCell(8, 2)->getX());

        $this->expectException(\OutOfBoundsException::class);
        $sudoku->getCell(9, 2);
    }

    public static function sudokuProvider(): iterable
    {
        foreach (Puzzle::cases() as $puzzle) {
            yield ['name' => $puzzle->name, 'puzzle' => $puzzle->value];
        }
    }
}
