<?php

namespace Endroid\Sudoku;

class Puzzle {

    protected $time;
    protected $cells;
    protected $cellsSolved = 0;

    protected $moves = array();
    protected $optionsRemovedByMove = array();
    protected $cellsSolvedByMove = array();

    public function __construct($values = null) {

		// First create all cells
		for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
	        $this->cells[$rowIndex] = array();
			for ($colIndex = 0; $colIndex < 9; $colIndex++) {
				$this->cells[$rowIndex][$colIndex] = new Cell($rowIndex, $colIndex, $this);
	        }
	    }

	    // Set the adjacent cells
	    for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
			for ($colIndex = 0; $colIndex < 9; $colIndex++) {

				// Row cells
				for ($adjacentColIndex = 0; $adjacentColIndex < 9; $adjacentColIndex++) {
					if ($adjacentColIndex != $colIndex) {
						$this->cells[$rowIndex][$colIndex]->addAdjacentCell($this->cells[$rowIndex][$adjacentColIndex]);
					}
				}

				// Column cells
				for ($adjacentRowIndex = 0; $adjacentRowIndex < 9; $adjacentRowIndex++) {
					if ($adjacentRowIndex != $rowIndex) {
						$this->cells[$rowIndex][$colIndex]->addAdjacentCell($this->cells[$adjacentRowIndex][$colIndex]);
					}
				}

				// Block cells
                $rowIndexStart = floor($rowIndex / 3) * 3;
				$colIndexStart = floor($colIndex / 3) * 3;
				for ($adjacentRowIndex = $rowIndexStart; $adjacentRowIndex < $rowIndexStart + 3; $adjacentRowIndex++) {
					for ($adjacentColIndex = $colIndexStart; $adjacentColIndex < $colIndexStart + 3; $adjacentColIndex++) {
						if ($adjacentRowIndex != $rowIndex && $adjacentColIndex != $colIndex) {
							$this->cells[$rowIndex][$colIndex]->addAdjacentCell($this->cells[$adjacentRowIndex][$adjacentColIndex]);
						}
					}
				}

			}
	    }

        // If the input is a string, convert it to an array
        if (is_string($values)) {
            $values = $this->toArray($values);
        }

	    // Set the initial values
	    if ($values !== null) {
			for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
				for ($colIndex = 0; $colIndex < 9; $colIndex++) {
					if (intval($values[$rowIndex][$colIndex]) > 0) {
						$this->cells[$rowIndex][$colIndex]->setValue($values[$rowIndex][$colIndex]);
					}
				}
			}
		}
	}

    protected function toArray($values)
    {
        // Filter all but digits
        $values = preg_replace('#[^0-9]*#i', '', $values);

        // Create the rows
        $values = str_split($values, 9);

        // Create the columns
        foreach ($values as &$row) {
            $row = str_split($row);
        }

        return $values;
    }

    public function setCellSolved($cell)
    {
        $this->cellsSolvedByMove[count($this->moves)][] = array($cell, $cell->getOptions());
        $this->cellsSolved++;
    }

    public function setOptionRemoved($cell, $option)
    {
        $this->optionsRemovedByMove[count($this->moves)][] = array($cell, $option);
    }

    public function solve($depth = 0)
    {
        if ($depth == 0) {
            $time = microtime(true);
        }

        $progress = true;
        while ($progress) {
            $progress = false;
            for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
                for ($colIndex = 0; $colIndex < 9; $colIndex++) {
                    if ($this->cells[$rowIndex][$colIndex]->getShouldUpdateAdjacentCells()) {
                        $this->cells[$rowIndex][$colIndex]->updateAdjacentCells();
                        $progress = true;
                    }
                }
            }
        }

        $movesByCount = array();

        // Calculate all possible moves
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            for ($colIndex = 0; $colIndex < 9; $colIndex++) {
                $optionCount = count($this->cells[$rowIndex][$colIndex]->getOptions());
                foreach ($this->cells[$rowIndex][$colIndex]->getOptions() as $option) {
                    if ($this->cells[$rowIndex][$colIndex]->getValue() === null) {
                        $movesByCount[$optionCount][] = array($rowIndex, $colIndex, $option);
                    }
                }
            }
        }

        ksort($movesByCount);

        foreach ($movesByCount as &$item) {
            shuffle($item);
        }

        foreach ($movesByCount as $moves) {
            foreach ($moves as $move) {
                $this->moves[count($this->moves)] = $move;
                $moveCount = count($this->moves);
                $this->optionsRemovedByMove[$moveCount] = array();
                $this->cellsSolvedByMove[$moveCount] = array();
                $this->cells[$move[0]][$move[1]]->setValue($move[2]);
                try {
                    return $this->solve($depth + 1);
                } catch (\Exception $exception) {
                    $this->undo();
                }
            }
        }

        if ($depth != 0) {
            throw new \Exception('No valid options left');
        } else {
            $this->time = round(microtime(true) - $time, 3);
        }

        return $this;
    }

    protected function undo()
    {
        $moveCount = count($this->moves);

        foreach ($this->cellsSolvedByMove[$moveCount] as $item) {
            $item[0]->setValue(null);
            $item[0]->setOptions($item[1]);
            $this->cellsSolved--;
        }

        foreach ($this->optionsRemovedByMove[$moveCount] as $item) {
            $item[0]->addOption($item[1]);
        }

        unset($this->optionsRemovedByMove[$moveCount]);
        unset($this->cellsSolvedByMove[$moveCount]);
        unset($this->moves[$moveCount - 1]);
    }

    public function __toString()
    {
        $html = '<table>';
        for ($row = 0; $row < 9; $row++) {
            $html .= '<tr>';
            for ($col = 0; $col < 9; $col++) {
                $html .= '<td style="text-align: center; vertical-align: middle; width: 50px; height: 60px; border: 2px solid #000;">';
                $html .= $this->cells[$row][$col];
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table><br />';
        return $html;
    }

    public function isSolved()
    {
        return ($this->cellsSolved == 81);
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getValue($rowIndex, $colIndex)
    {
        return $this->cells[$rowIndex][$colIndex]->getValue();
    }
}