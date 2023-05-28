<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type;

final class IntegerExpression implements Expression
{
    public function __construct(
        public readonly int $value,
    ) {
    }

    public function type(): Type
    {
        return Type::INTEGER;
    }
}
