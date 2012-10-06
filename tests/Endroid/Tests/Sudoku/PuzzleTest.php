<?php

namespace Endroid\Tests\Sudoku;

use Endroid\Sudoku\Puzzle;

class PuzzleTest extends \PHPUnit_Framework_TestCase
{
    public function testSolveEasy()
    {
        // Add speed constraint
        set_time_limit(60);

        // An easy puzzle: involves no guessing
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
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }

    // Puzzle: hardest
    public function testSolveHard1()
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
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }

    // Puzzle: platinum blonde
    public function testSolveHard2()
    {
        // Add speed constraint
        set_time_limit(60);

        // A difficult puzzle
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

        // Create the object
        $sudoku = new Puzzle($values);
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }

    // Puzzle: golden nugget
    public function testSolveHard3()
    {
        // Add speed constraint
        set_time_limit(60);

        // A difficult puzzle
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

        // Create the object
        $sudoku = new Puzzle($values);
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }

    // Puzzle: red dwarf
    public function testSolveHard4()
    {
        // Add speed constraint
        set_time_limit(60);

        // A difficult puzzle
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

        // Create the object
        $sudoku = new Puzzle($values);
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());
    }
}
