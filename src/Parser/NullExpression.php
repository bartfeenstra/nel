<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type\NullType;
use Bartfeenstra\Nel\Type\Type;

final class NullExpression implements Expression
{
    public function type(): Type
    {
        return new NullType();
    }
}
