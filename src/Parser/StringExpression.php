<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type;

final class StringExpression implements Expression
{
    public function __construct(
        public readonly string $value,
    ) {
    }

    public function type(): Type
    {
        return Type::STRING;
    }
}
