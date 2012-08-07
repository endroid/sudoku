<?php

namespace Endroid\Tests\Sudoku;

use Endroid\Sudoku\Puzzle;

class PuzzleTest extends \PHPUnit_Framework_TestCase
{
    public function testSolveEasy()
    {
        // Add speed constraint
        set_time_limit(60);

        // An easy puzzle
        $values = '
            003020600
            900305001
            001806400
            008102900
            700000008
            006708200
            002609500
            800203009
            005010300';

        // Create the object
        $sudoku = new Puzzle($values);
        $sudoku = $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }

    public function testSolveHard()
    {
        // Add speed constraint
        set_time_limit(60);

        // A difficult puzzle
        $values = '
            800000000
            003600000
            070090200
            050007000
            000045700
            000100030
            001000068
            008500010
            090000400';

        // Create the object
        $sudoku = new Puzzle($values);
        $sudoku = $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }
}
