<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Factory;

use Endroid\Sudoku\Board\Board;
use Endroid\Sudoku\Board\Cell;
use Endroid\Sudoku\Board\Section;

final class BoardFactory
{
    public static function create(): Board
    {
        return new Board();
    }

    public static function createDefault(): Board
    {
        $board = self::create();

        $cellsPerSection = [];
        for ($rowIndex = 0; $rowIndex < 9; ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < 9; ++$columnIndex) {
                $cell = new Cell();
                $blockIndex = intval(floor($rowIndex / 3) * 3 + floor($columnIndex / 3));
                $cellsPerSection['row-'.$rowIndex][] = $cell;
                $cellsPerSection['column-'.$columnIndex][] = $cell;
                $cellsPerSection['block-'.$blockIndex][] = $cell;
            }
        }

        foreach ($cellsPerSection as $sectionName => $cells) {
            $section = new Section($cells);
            $section->setName($sectionName);
            $board->addSection($section);
        }

        return $board;
    }
}
