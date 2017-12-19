<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\InvalidAssignmentException;

final class Cell
{
    public $value = 0;
    public $valueIsPropagated = false;

    public $options = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9];

    public $adjacentCells = [];

    /** @var Row */
    public $row;

    /** @var Column */
    public $column;

    /** @var Block */
    public $block;

    /** @var AbstractSection[] */
    public $sections = [];

    public $sudoku;

    public $moves = [];

    public function __construct(Row $row, Column $column, Block $block, Sudoku $sudoku)
    {
        $this->sections[] = $this->row = $row;
        $this->sections[] = $this->column = $column;
        $this->sections[] = $this->block = $block;

        $this->row->addCell($this);
        $this->column->addCell($this);
        $this->block->addCell($this);

        $this->sudoku = $sudoku;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getRowIndex(): int
    {
        return $this->row->getIndex();
    }

    public function getColumnIndex(): int
    {
        return $this->column->getIndex();
    }

    public function setAdjacentCells()
    {
        foreach ($this->sections as $section) {
            foreach ($section->getCells() as $cell) {
                if ($cell != $this && !in_array($cell->key, $this->adjacentCells)) {
                    $this->adjacentCells[$cell->key] = $cell;
                }
            }
        }
    }

    public function setValue(int $value, bool $propagate = false): void
    {
        if ($this->value === $value) {
            return;
        }

        if ($this->value > 0 && $this->value !== $value) {
            throw new InvalidAssignmentException('Setting cell value ['.$this->row->getIndex().', '.$this->column->getIndex().'] to '.$value.' while it was already set to '.$this->value);
        }

        $this->storeMove();

        $this->value = $value;

        if ($propagate) {
            $this->propagateValue();
        }
    }

    public function propagateValue(): void
    {
        if ($this->valueIsPropagated) {
            return;
        }

        foreach ($this->options as $option) {
            if ($option !== $this->value) {
                $this->removeOption($option);
            }
        }

        foreach ($this->sections as $section) {
            $section->valueSet($this->value);
        }

        foreach ($this->adjacentCells as $cell) {
            $cell->removeOption($this->value);
        }

        $this->valueIsPropagated = true;
    }

    public function removeOption($option)
    {
        if (!isset($this->options[$option])) {
            return;
        }

        $this->storeMove();

        unset($this->options[$option]);

        if (0 === $this->value && 0 == count($this->options)) {
            throw new \Exception('Cell '.$this->key.' has no options left');
        }

        $this->sudoku->addOptionRemoved($this, $option);
    }

    public function propagateOptionRemoved($option)
    {
        if (1 == count($this->options) && 0 === $this->value) {
            $this->sudoku->setCellValue($this->getRowIndex(), $this->getColumnIndex(), end($this->options), true);
        }

        foreach ($this->sections as $section) {
            $section->checkUnique($option);
            $section->checkAvailableOptions();
        }
    }

    public function storeMove()
    {
        $moveIndex = $this->sudoku->moveIndex;
        if ($moveIndex > -1 && !isset($this->moves[$moveIndex])) {
            $this->moves[$moveIndex] = [$this->options, $this->value];
        }
    }

    public function undoMove()
    {
        $moveIndex = $this->sudoku->moveIndex;
        if ($moveIndex > -1 && isset($this->moves[$moveIndex])) {
            $this->options = $this->moves[$moveIndex][0];
            $this->value = $this->moves[$moveIndex][1];
            unset($this->moves[$moveIndex]);
        }
    }

    public function __toString()
    {
        return strval($this->value);
    }

    public function toArray()
    {
        return [
            'value' => 0 === $this->value ? '' : $this->value,
            'options' => $this->options,
        ];
    }
}
