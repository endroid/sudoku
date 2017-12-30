<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku\Board;

use Endroid\Sudoku\Exception\InvalidCellValueException;
use Endroid\Sudoku\Exception\NoMoreOptionsLeftException;
use Ramsey\Uuid\Uuid;

final class Cell
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
     * @var int
     */
    private $value;

    /**
     * @var int[]
     */
    private $options;

    /**
     * @var Board[]
     */
    private $boards;

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
        $this->id = Uuid::uuid4()->toString();

        $this->value = 0;
        $this->options = [];
        $this->boards = [];
        $this->sections = [];
        $this->adjacentCells = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        if (!isset($this->options[$value])) {
            throw new InvalidCellValueException(sprintf('Invalid value %s for cell %s, available options are [%s]', $value, $this->name, implode(',', $this->options)));
        }

        $this->value = $value;
        $this->options = [$value => $value];
    }











    public function setState(int $value, array $options): void
    {
        $this->value = $value;
        $this->options = $options;
    }



    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function removeOption(int $option): void
    {
        echo 'Removing option '.$option.' for cell '.$this->getIndex().'<br />';

        unset($this->options[$option]);

        if (count($this->options) === 1 && $this->value === 0) {
            $this->setValue(current($this->options));
        }

        if (count($this->options) === 0) {
            throw new NoMoreOptionsLeftException(sprintf('No more options left for cell %s', $this->index));
        }
    }

    public function setPosition(Row $row, Column $column, Block $block): void
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
