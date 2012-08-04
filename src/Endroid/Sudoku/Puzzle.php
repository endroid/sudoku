<?php

namespace Endroid\Sudoku;

class Puzzle {

	protected $cells;
    protected $cellsSolved = 0;
    protected $debug = false;
    protected $isSolved = false;

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

    public function incrementCellsSolved()
    {
        $this->cellsSolved++;
    }

    public function isSolved()
    {
        return ($this->cellsSolved == 81);
    }

    public function solve($depth = 0)
    {
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

        if ($progress && !$this->isSolved()) {
            return $this->solve($depth + 1);
        }

        /**
         * No more simple progress possible : proceed by guessing
         */

        $moves = array();

        // Calculate all possible moves
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            for ($colIndex = 0; $colIndex < 9; $colIndex++) {
                $optionCount = count($this->cells[$rowIndex][$colIndex]->getOptions());
                foreach ($this->cells[$rowIndex][$colIndex]->getOptions() as $option) {
                    if ($this->cells[$rowIndex][$colIndex]->getValue() === null) {
                        $moves[$optionCount][] = array($rowIndex, $colIndex, $option);
                    }
                }
            }
        }

        // Least options first
        krsort($moves);

        // Try each move
        $progress = true;
        while ($progress) {

            $progress = false;
            foreach ($moves as $option => $option_moves) {

                $move = array_pop($moves[$option]);

                if (!$move) {
                    echo count($moves[$option]);
                    break;
                }

                $sudoku = new Puzzle();
                $sudoku->copy($this);

                try {

                    $sudoku->cells[$move[0]][$move[1]]->setValue($move[2]);
                    $sudoku = $sudoku->solve($depth + 1);

                    if ($sudoku->isSolved()) {
                        return $sudoku;
                    }

                } catch (\Exception $exception) { }
            }
        }

        return $this;
    }

    protected function copy($sudoku)
    {
        for ($rowIndex = 0; $rowIndex < 9; $rowIndex++) {
            for ($colIndex = 0; $colIndex < 9; $colIndex++) {
                if ($sudoku->cells[$rowIndex][$colIndex]->getValue() !== null) {
                    $this->cells[$rowIndex][$colIndex]->setValue($sudoku->cells[$rowIndex][$colIndex]->getValue());
                    $this->cells[$rowIndex][$colIndex]->setShouldUpdateAdjacentCells(false);
                }
                $this->cells[$rowIndex][$colIndex]->setOptions($sudoku->cells[$rowIndex][$colIndex]->getOptions());
            }
        }
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
}