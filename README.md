Endroid Sudoku Solver
=====================

Example usage
-------------

``` php
<?php
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

// And finally solve the puzzle
echo $sudoku->solve();
