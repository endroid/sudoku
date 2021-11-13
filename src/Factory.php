<?php

declare(strict_types=1);

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
    public const NO_GUESSING = '..3.2.6..9..3.5..1..18.64....81.29..7.......8..67.82....26.95..8..2.3..9..5.1.3..';
    public const PLATINUM_BLONDE = '.......12........3..23..4....18....5.6..7.8.......9.....85.....9...4.5..47...6...';
    public const GOLDEN_NUGGET = '.......39.....1..5..3.5.8....8.9...6.7...2...1..4.......9.8..5..2....6..4..7.....';
    public const RED_DWARF = '12.3....435....1....4........54..2..6...7.........8.9...31..5.......9.7.....6...8';

    /**
     * @throws InvalidStringRepresentationException
     */
    public function createFromString(string $values): Sudoku
    {
        $values = (string) preg_replace('#[^0-9\.-]*#i', '', $values);

        $base = (int) sqrt(strlen($values));
        if ($base * $base !== strlen($values)) {
            throw InvalidStringRepresentationException::create($values);
        }

        $values = array_chunk(array_map('intval', str_split($values)), $base);

        $sections = [];
        $sudoku = new Sudoku();
        for ($row = 0; $row < $base; ++$row) {
            for ($column = 0; $column < $base; ++$column) {
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

    /** @return array<string, string> */
    public function getExamples(): array
    {
        return [
            'no_guessing' => self::NO_GUESSING,
            'golden_nugget' => self::GOLDEN_NUGGET,
            'red_dwarf' => self::RED_DWARF,
            'platinum_blonde' => self::PLATINUM_BLONDE,
        ];
    }
}
