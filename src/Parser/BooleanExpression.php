<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type;

final class BooleanExpression implements Expression
{
    public function __construct(
        public readonly bool $value,
    ) {
    }

    public function type(): Type
    {
        return Type::BOOLEAN;
    }
}
