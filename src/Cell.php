<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

class Cell
{
    private $x;
    private $y;

    private $value;
    private $options;

    public function __construct(int $x, int $y, int $base = 9)
    {
        $this->x = $x;
        $this->y = $y;

        for ($option = 1; $option <= $base; $option++) {
            $this->options[$option] = $option;
        }
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
        $this->options = [$value];
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function getOptions(): \Iterator
    {
        foreach ($this->options as $option) {
            yield $option;
        }
    }

    public function removeOption(int $option): void
    {
        unset($this->options[$option]);

        if (count($this->options) === 1) {
            $this->value = current($this->options);
        }
    }

    public function isEmpty(): bool
    {
        return $this->value === null;
    }

    public function isFilled(): bool
    {
        return !$this->isEmpty();
    }
}
