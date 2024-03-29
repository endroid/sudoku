<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

enum Puzzle: string
{
    case NoGuessing = '..3.2.6..9..3.5..1..18.64....81.29..7.......8..67.82....26.95..8..2.3..9..5.1.3..';
    case PlatinumBlonde = '.......12........3..23..4....18....5.6..7.8.......9.....85.....9...4.5..47...6...';
    case GoldenNugget = '.......39.....1..5..3.5.8....8.9...6.7...2...1..4.......9.8..5..2....6..4..7.....';
    case RedDwarf = '12.3....435....1....4........54..2..6...7.........8.9...31..5.......9.7.....6...8';
}
