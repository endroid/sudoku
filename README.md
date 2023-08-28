# Sudoku

*By [endroid](https://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/sudoku.svg)](https://packagist.org/packages/endroid/sudoku)
[![Build Status](https://github.com/endroid/sudoku/workflows/CI/badge.svg)](https://github.com/endroid/sudoku/actions)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/sudoku.svg)](https://packagist.org/packages/endroid/sudoku)
[![License](http://img.shields.io/packagist/l/endroid/sudoku.svg)](https://packagist.org/packages/endroid/sudoku)

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

// Load a puzzle
$sudoku = new Sudoku('
    000000012
    000000003
    002300400
    001800005
    060070800
    000009000
    008500000
    900040500
    470006000
');

// Solve the puzzle
$solver = new Solver($sudoku);
$solver->solve();
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatibility
breaking changes will be kept to a minimum but be aware that these can occur.
Lock your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.