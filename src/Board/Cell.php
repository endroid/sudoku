<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Board;

use Endroid\Sudoku\Exception\InvalidCellValueException;

final class Cell
{
    private $index;
    private $value;
    private $options;

    /**
     * @var Section
     */
    private $row;

    /**
     * @var Section
     */
    private $column;

    /**
     * @var Section
     */
    private $block;

    /**
     * @var Section[]
     */
    private $sections;

    /**
     * @var Cell[]
     */
    private $adjacentCells;

    public function __construct()
    {
        $this->value = 0;
        $this->options = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9
        ];
        $this->sections = [];
        $this->adjacentCells = [];
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        if (!isset($this->options[$value])) {
            throw new InvalidCellValueException(sprintf('Invalid value %s for cell, available options are [%s]', $value, implode(',', $this->options)));
        }

        $this->value = $value;
        $this->options = [$value => $value];
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function removeOption(int $option): void
    {
        unset($this->options[$option]);

        if (count($this->options) === 1 && $this->value === 0) {
            $this->setValue(current($this->options));
        }
    }

    public function setPosition(Section $row, Section $column, Section $block): void
    {
        $this->row = $row;
        $this->column = $column;
        $this->block = $block;

        $this->index = $row->getIndex().$column->getIndex();
        $this->sections = [$row, $column, $block];

        $row->addCell($this);
        $column->addCell($this);
        $block->addCell($this);
    }

    public function getRow(): Section
    {
        return $this->row;
    }

    public function getColumn(): Section
    {
        return $this->column;
    }

    /**
     * @return Cell[]
     */
    public function getAdjacentCells(): array
    {
        return $this->adjacentCells;
    }

    public function addAdjacentCell(Cell $cell): void
    {
        $this->adjacentCells[$cell->getIndex()] = $cell;
    }

//
//    public function propagateValue(): void
//    {
//        if ($this->valueIsPropagated) {
//            return;
//        }
//
//        foreach ($this->options as $option) {
//            if ($option !== $this->value) {
//                $this->removeOption($option);
//            }
//        }
//
//        foreach ($this->sections as $section) {
//            $section->valueSet($this->value);
//        }
//
//        foreach ($this->adjacentCells as $cell) {
//            $cell->removeOption($this->value);
//        }
//
//        $this->valueIsPropagated = true;
//    }
//
//    public function removeOption($option)
//    {
//        if (!isset($this->options[$option])) {
//            return;
//        }
//
//        $this->storeMove();
//
//        unset($this->options[$option]);
//
//        if (0 === $this->value && 0 == count($this->options)) {
//            throw new \Exception('Cell '.$this->key.' has no options left');
//        }
//
//        $this->sudoku->addOptionRemoved($this, $option);
//    }
//
//    public function propagateOptionRemoved($option)
//    {
//        if (1 == count($this->options) && 0 === $this->value) {
//            $this->sudoku->setCellValue($this->getRowIndex(), $this->getColumnIndex(), end($this->options), true);
//        }
//
//        foreach ($this->sections as $section) {
//            $section->validateUniqueness($option);
//            $section->validateAvailableOptions();
//        }
//    }
//
//    public function storeMove()
//    {
//        $moveIndex = $this->sudoku->moveIndex;
//        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
//            $this->moves[$moveIndex] = [$this->options, $this->value];
//        }
//    }
//
//    public function undoMove()
//    {
//        $moveIndex = $this->sudoku->moveIndex;
//        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
//            $this->options = $this->moves[$moveIndex][0];
//            $this->value = $this->moves[$moveIndex][1];
//            unset($this->moves[$moveIndex]);
//        }
//    }
//
//    public function __toString()
//    {
//        return strval($this->value);
//    }
//
//    public function toArray()
//    {
//        return [
//            'value' => 0 === $this->value ? '' : $this->value,
//            'options' => $this->options,
//        ];
//    }
}
