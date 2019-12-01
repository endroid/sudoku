<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Tests;

use Endroid\Sudoku\Factory;
use Endroid\Sudoku\Solver;
use PHPUnit\Framework\TestCase;

class SolverTest extends TestCase
{
    /**
     * @dataProvider sudokuProvider
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

    public function sudokuProvider()
    {
        $sudokus = [];

        $factory = new Factory();
        foreach ($factory->getExamples() as $name => $sudoku) {
            $sudokus[] = [
                'name' => $name,
                'sudoku' => $sudoku,
            ];
        }

        return $sudokus;
    }
}
