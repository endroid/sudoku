<?php

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
    public function testSolver()
    {
        set_time_limit(60);

        $factory = new Factory();
        $examples = $factory->getExamples();
        foreach ($examples as $example) {
            $sudoku = $factory->createFromString($example);
            $solver = new Solver($sudoku);
            $solver->solve();
            $this->assertTrue($solver->isSolved());
        }
    }
}
