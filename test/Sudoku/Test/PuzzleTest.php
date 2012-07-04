<?php

namespace Sudoku\Test;

use Sudoku\Puzzle;

class CurlTest extends \PHPUnit_Framework_TestCase
{
    public function testSolveEasy()
    {
        // An easy puzzle
        $sudoku = '
            003020600
            900305001
            001806400
            008102900
            700000008
            006708200
            002609500
            800203009
            005010300';

        // Filter all but digits
        $sudoku = preg_replace('#[^0-9]*#i', '', $sudoku);

        // Create the rows
        $sudoku = str_split($sudoku, 9);

        // Create the columns
        foreach ($sudoku as &$row) {
            $row = str_split($row);
        }

        // Create the object
        $sudoku = new \Sudoku\Puzzle($sudoku);
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());

    }

    public function testSolveHard()
    {
        // An easy puzzle
        $sudoku = '
            800000000
            003600000
            070090200
            050007000
            000045700
            000100030
            001000068
            008500010
            090000400';

        // Filter all but digits
        $sudoku = preg_replace('#[^0-9]*#i', '', $sudoku);

        // Create the rows
        $sudoku = str_split($sudoku, 9);

        // Create the columns
        foreach ($sudoku as &$row) {
            $row = str_split($row);
        }

        // Create the object
        $sudoku = new \Sudoku\Puzzle($sudoku);
        $sudoku->solve();

        // Check if the puzzle is solved
        $this->assertTrue($sudoku->isSolved());

    }
}
