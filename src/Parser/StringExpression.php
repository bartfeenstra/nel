<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type\StringType;
use Bartfeenstra\Nel\Type\Type;

final class StringExpression implements Expression
{
    public function __construct(
        public readonly string $value,
    ) {
    }

    public function type(): Type
    {
        return new StringType();
    }
}
