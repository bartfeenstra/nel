<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type;

final class NullExpression implements Expression
{
    public function type(): Type
    {
        return Type::NULL;
    }
}
