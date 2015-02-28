Endroid Sudoku Solver
=====================

*By [endroid](http://endroid.nl/)*

[![Build Status](https://secure.travis-ci.org/endroid/Sudoku.png)](http://travis-ci.org/endroid/Sudoku)
[![Latest Stable Version](https://poser.pugx.org/endroid/sudoku/v/stable.png)](https://packagist.org/packages/endroid/sudoku)
[![Total Downloads](https://poser.pugx.org/endroid/sudoku/downloads.png)](https://packagist.org/packages/endroid/sudoku)

The library that solves Sudoku puzzles in a jiffy.

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/sudoku
```

## Usage

``` php
<?php

use Endroid\Sudoku\Puzzle;

// An difficult puzzle (Platinum Blonde)
$puzzle = '
    000000012
    000000003
    002300400
    001800005
    060070800
    000009000
    008500000
    900040500
    470006000';

// Create the puzzle
$sudoku = new Puzzle($puzzle);

// Solve the puzzle
echo $sudoku->solve();
```

## Versioning

Semantic versioning ([semver](http://semver.org/)) is applied.

## License

This bundle is under the MIT license. For the full copyright and license information, please view the LICENSE file that
was distributed with this source code.
