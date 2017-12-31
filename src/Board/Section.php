<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Board;

use Ramsey\Uuid\Uuid;

final class Section
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Cell[]
     */
    private $cells;

    /**
     * @var Board[]
     */
    private $boards;

    public function __construct(array $cells)
    {
        $this->id = Uuid::uuid4()->toString();

        foreach ($cells as $cell) {
            $cell->addSection($this);
            $this->cells[$cell->getId()] = $cell;
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCells(): array
    {
        return $this->cells;
    }

    public function addCell(Cell $cell): void
    {
        foreach ($this->cells as $adjacentCell) {
            $adjacentCell->addAdjacentCell($cell);
            $cell->addAdjacentCell($adjacentCell);
        }

        $this->cells[] = $cell;
    }

    public function addBoard(Board $board): void
    {
        $this->boards[$board->getId()] = $board;
    }

//    public function valueSet(int $value): void
//    {
//        if (!isset($this->availableValues[$value])) {
//            throw new InvalidAssignmentException('Value '.$value.' not available for section '.get_class($this).' '.$this->index);
//        }
//
//        $this->storeMove();
//
//        unset($this->availableValues[$value]);
//    }

//    private function storeMove()
//    {
//        $moveIndex = $this->sudoku->moveIndex;
//        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
//            $this->moves[$moveIndex] = array($this->availableValues);
//        }
//    }
//
//    public function undoMove()
//    {
//        $moveIndex = $this->sudoku->moveIndex;
//        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
//            $this->availableValues = $this->moves[$moveIndex][0];
//            unset($this->moves[$moveIndex]);
//        }
//    }
}
