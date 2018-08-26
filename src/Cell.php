<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\NoOptionsLeftException;

class Cell
{
    private $x;
    private $y;

    private $options;

    public function __construct(int $x, int $y, int $base = 9)
    {
        $this->x = $x;
        $this->y = $y;

        for ($option = 1; $option <= $base; ++$option) {
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

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function hasOption(int $option): bool
    {
        return isset($this->options[$option]);
    }

    public function removeOption(int $option): void
    {
        unset($this->options[$option]);

        if (count($this->options) === 0) {
            throw new NoOptionsLeftException();
        }
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setValue(int $value): void
    {
        $this->options = [$value => $value];
    }

    public function getValue(): ?int
    {
        if (count($this->options) === 1) {
            return current($this->options);
        }

        return null;
    }

    public function isEmpty(): bool
    {
        return null === $this->getValue();
    }

    public function isFilled(): bool
    {
        return !$this->isEmpty();
    }

    public function __toString(): string
    {
        return '['.$this->x.','.$this->y.':'.implode(',', $this->options).']';
    }
}
