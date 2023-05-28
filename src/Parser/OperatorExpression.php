<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Operator\Operator;

abstract class OperatorExpression implements Expression
{
    public function __construct(
        public readonly Operator $operator,
    ) {
    }
}
