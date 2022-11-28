<?php

declare(strict_types=1);

namespace Endroid\Sudoku;

use Endroid\Sudoku\Exception\NoOptionsLeftException;

final class Cell implements \Stringable
{
    /** @var array<int, int> */
    private array $options;

    public function __construct(
        private int $x,
        private int $y,
        int $base = 9
    ) {
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
