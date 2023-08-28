<?php

declare(strict_types=1);

namespace Endroid\Sudoku\Tests;

use Endroid\Sudoku\Factory;
use Endroid\Sudoku\Solver;
use PHPUnit\Framework\TestCase;

final class SolverTest extends TestCase
{
    /**
     * @dataProvider sudokuProvider
     *
     * @testdox Solving sudoku "$name"
     */
    public function testSolver($name, $sudoku)
    {
        set_time_limit(60);

        $factory = new Factory();
        $sudoku = $factory->createFromString($sudoku);
        $solver = new Solver($sudoku);
        $solver->solve();
        $this->assertTrue($solver->isSolved());
    }

    public static function sudokuProvider(): iterable
    {
        $factory = new Factory();
        foreach ($factory->getExamples() as $name => $sudoku) {
            yield ['name' => $name, 'sudoku' => $sudoku];
        }
    }
}
