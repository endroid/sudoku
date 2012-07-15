<?php

namespace Sudoku;

class Cell
{
    protected $rowIndex;
    protected $colIndex;
    protected $sudoku;
    protected $value = null;
    protected $shouldUpdateAdjacentCells = false;
    protected $options = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9);
    protected $adjacentCells = array();

    public function __construct($rowIndex, $colIndex, $sudoku)
    {
        $this->rowIndex = $rowIndex;
		$this->colIndex = $colIndex;
        $this->sudoku = $sudoku;
	}

    public function addAdjacentCell(Cell $cell)
    {
		$this->adjacentCells[] = $cell;
	}

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value, $updateAdjacentCells = false)
    {
        if ($this->value != null) {
            throw new \Exception('Value was already set');
        }
        $this->value = $value;
        $this->sudoku->incrementCellsSolved();
        $this->options = array($value => $value);
        $this->shouldUpdateAdjacentCells = true;
        if ($updateAdjacentCells) {
            $this->updateAdjacentCells();
        }
	}

    public function setShouldUpdateAdjacentCells($shouldUpdateAdjacentCells)
    {
        $this->shouldUpdateAdjacentCells = $shouldUpdateAdjacentCells;
    }

    public function getShouldUpdateAdjacentCells()
    {
        return $this->shouldUpdateAdjacentCells;
    }

    public function updateAdjacentCells()
    {
        foreach ($this->adjacentCells as $cell) {
            $cell->removeOption($this->value);
        }
        $this->shouldUpdateAdjacentCells = false;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    protected function removeOption($option)
    {
        unset($this->options[$option]);
        if (count($this->options) == 0) {
            throw new \Exception('Invalid Sudoku, no more options left');
        }
        $this->checkUnique();
        $this->checkSolved();
        $this->checkValidAdjacent();
    }

    protected function checkUnique()
    {
        if ($this->value === null) {
            foreach ($this->options as $option) {
                $rowUnique = true;
                $colUnique = true;
                $blockUnique = true;
                foreach ($this->adjacentCells as $cell) {
                    if (in_array($option, $cell->options)) {
                        if ($cell->rowIndex == $this->rowIndex) {
                            $rowUnique = false;
                        }
                        if ($cell->colIndex == $this->colIndex) {
                            $colUnique = false;
                        }
                        $rowIndexStart = floor($this->rowIndex / 3) * 3;
                        $colIndexStart = floor($this->colIndex / 3) * 3;
                        if ($cell->rowIndex >= $rowIndexStart && $cell->rowIndex < $rowIndexStart + 3 && $cell->colIndex >= $colIndexStart && $cell->colIndex < $colIndexStart + 3) {
                            $blockUnique = false;
                        }
                    }
                }
                if ($rowUnique || $colUnique || $blockUnique) {
                    $this->setValue($option);
                    break;
                }
            }
        }
    }

    protected function checkSolved()
    {
        if ($this->value === null && count($this->options) == 1) {
            $this->setValue(end($this->options));
        }
    }

    protected function checkValidAdjacent()
    {
        foreach ($this->adjacentCells as $cell) {
            if ($cell->value !== null && $cell->value == $this->value) {
                throw new \Exception('Invalid Sudoku, value already taken');
            }
        }
    }

    public function __toString()
    {
        $html = '';
        if ($this->value !== null) {
            $html .= '<div class="solved">'.$this->value.'</div>';
        } else {
            $html .= '<div class="options">'.implode(' ', $this->options).'</div>';
        }
        return $html;
    }

}
?>