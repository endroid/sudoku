Endroid Sudoku Solver
=====================

The library that solves Sudoku puzzles in a jiffy.

Example usage
-------------

``` php
<?php
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
$sudoku = new \Sudoku\Puzzle($values);

// And finally solve the puzzle
echo $sudoku->solve();
