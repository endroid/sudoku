<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\InvalidStringRepresentationException;

class Factory
{
    const EXAMPLE_NO_GUESSING = '. . 3 . 2 . 6 . . 9 . . 3 . 5 . . 1 . . 1 8 . 6 4 . . . . 8 1 . 2 9 . . 7 . . . . . . . 8 . . 6 7 . 8 2 . . . . 2 6 . 9 5 . . 8 . . 2 . 3 . . 9 . . 5 . 1 . 3 . .';
    const EXAMPLE_PLATINUM_BLONDE = '. . . . . . . 1 2 . . . . . . . . 3 . . 2 3 . . 4 . . . . 1 8 . . . . 5 . 6 . . 7 . 8 . . . . . . . 9 . . . . . 8 5 . . . . . 9 . . . 4 . 5 . . 4 7 . . . 6 . . .';
    const EXAMPLE_GOLDEN_NUGGET = '. . . . . . . 3 9 . . . . . 1 . . 5 . . 3 . 5 . 8 . . . . 8 . 9 . . . 6 . 7 . . . 2 . . . 1 . . 4 . . . . . . . 9 . 8 . . 5 . . 2 . . . . 6 . . 4 . . 7 . . . . .';
    const EXAMPLE_RED_DWARF = '1 2 . 3 . . . . 4 3 5 . . . . 1 . . . . 4 . . . . . . . . 5 4 . . 2 . . 6 . . . 7 . . . . . . . . . 8 . 9 . . . 3 1 . . 5 . . . . . . . 9 . 7 . . . . . 6 . . . 8';

    /**
     * @throws InvalidStringRepresentationException
     */
    public function createFromString(string $values): Sudoku
    {
        $values = preg_replace('#[^0-9\.-]*#i', '', $values);

        $base = (int) sqrt(strlen($values));
        if ($base * $base !== strlen($values)) {
            throw InvalidStringRepresentationException::create($values);
        }

        $values = array_chunk(array_map('intval', str_split($values)), $base);

        $sections = [];
        $sudoku = new Sudoku($base);
        for ($row = 0; $row < $base; $row++) {
            for ($column = 0; $column < $base; $column++) {
                $cell = $sudoku->createCell($column, $row);
                if ((int) $values[$row][$column] > 0) {
                    $cell->setValue($values[$row][$column]);
                }
                $sections['row-'.$row][] = $cell;
                $sections['column-'.$column][] = $cell;
                $sections['block-'.intval(floor($row / 3) * 3 + floor($column / 3))][] = $cell;
            }
        }

        foreach ($sections as $cells) {
            $sudoku->createSection($cells);
        }

        return $sudoku;
    }

    public function getExamples(): array
    {
        return [
            self::EXAMPLE_NO_GUESSING,
            self::EXAMPLE_PLATINUM_BLONDE,
            self::EXAMPLE_GOLDEN_NUGGET,
            self::EXAMPLE_RED_DWARF
        ];
    }
}
