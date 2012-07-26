Endroid Sudoku Solver
=====================

[![Build Status](https://secure.travis-ci.org/endroid/sudoku.png)](http://travis-ci.org/endroid/sudoku)

The library that solves Sudoku puzzles in a jiffy.

Example usage
-------------

``` php
<?php
// An easy puzzle
$puzzle = '
    003020600
    900305001
    001806400
    008102900
    700000008
    006708200
    002609500
    800203009
    005010300';

// Create the puzzle
$sudoku = new \Endroid\Sudoku\Puzzle($puzzle);

// Solve the puzzle
echo $sudoku->solve();
