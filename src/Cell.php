<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\NoOptionsLeftException;

final class Cell implements \Stringable
{
    public function __construct(
        private readonly int $x,
        private readonly int $y,
        /** @var array<int, int> */
        private array $options = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9],
    ) {
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    /** @param array<int, int> $options */
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

        if (0 === count($this->options)) {
            throw new NoOptionsLeftException();
        }
    }

    /** @return array<int> */
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
        if (1 === count($this->options)) {
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
