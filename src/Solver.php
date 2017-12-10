<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

final class Solver
{
    private $sudoku;

    public function __construct(Sudoku $sudoku)
    {
        $this->sudoku = $sudoku;
    }

    public function solve()
    {
        $this->sudoku->solve();
    }
}
