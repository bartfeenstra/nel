<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

use Stringable;

abstract class Type implements Stringable
{
    public function __toString(): string
    {
        return $this->phpType()->value;
    }

    abstract public function phpType(): PhpType;
}
