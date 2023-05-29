<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class BooleanExpression implements Expression
{
    public function __construct(
        public readonly bool $value,
    ) {
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
