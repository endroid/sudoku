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
use Endroid\Sudoku\Sudoku;
use PHPUnit\Framework\TestCase;

class SudokuTest extends TestCase
{
    public function testSolveWithoutGuessing()
    {
        set_time_limit(60);

        $sudoku = Factory::createFromString('
            003020600
            900305001
            001806400
            008102900
            700000008
            006708200
            002609500
            800203009
            005010300
        ');

        $solver = new Solver($sudoku);
        $solver->solve();

        $this->assertTrue($solver->isSolved());
    }

    public function testSolvePlatinumBlonde()
    {
        set_time_limit(60);

        $values = '
            000000012
            000000003
            002300400
            001800005
            060070800
            000009000
            008500000
            900040500
            470006000';

        $sudoku = Sudoku::fromString($values);
        $solver = new Solver($sudoku);
        $solver->solve();

        $this->assertTrue($sudoku->isSolved());
    }

    public function testSolveGoldenNugget()
    {
        set_time_limit(60);

        $values = '
            000000039
            000001005
            003050800
            008090006
            070002000
            100400000
            009080050
            020000600
            400700000';

        $sudoku = Sudoku::fromString($values);
        $solver = new Solver($sudoku);
        $solver->solve();

        $this->assertTrue($sudoku->isSolved());
    }

    public function testSolveRedDwarf()
    {
        set_time_limit(60);

        $values = '
            120300004
            350000100
            004000000
            005400200
            600070000
            000008090
            003100500
            000009070
            000060008';

        $sudoku = Sudoku::fromString($values);
        $solver = new Solver($sudoku);
        $solver->solve();

        $this->assertTrue($sudoku->isSolved());
    }
}
