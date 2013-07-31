Endroid Sudoku Solver
=====================

*By [endroid](http://endroid.nl/)*

[![Build Status](https://secure.travis-ci.org/endroid/Sudoku.png)](http://travis-ci.org/endroid/Sudoku)
[![Latest Stable Version](https://poser.pugx.org/endroid/sudoku/v/stable.png)](https://packagist.org/packages/endroid/sudoku)
[![Total Downloads](https://poser.pugx.org/endroid/sudoku/downloads.png)](https://packagist.org/packages/endroid/sudoku)

The library that solves Sudoku puzzles in a jiffy.

Example usage
-------------

``` php
<?php

use Endroid\Sudoku\Puzzle;

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
$sudoku = new Puzzle($puzzle);

// Solve the puzzle
echo $sudoku->solve();
```

## License

This bundle is under the MIT license. For the full copyright and license information, please view the LICENSE file that
was distributed with this source code.
