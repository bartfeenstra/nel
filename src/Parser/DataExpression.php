<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type\Type;

final class DataExpression implements Expression
{
    public function __construct(
        private readonly Type $type,
        public readonly string $name,
    ) {
    }

    public function type(): Type
    {
        return $this->type;
    }
}
