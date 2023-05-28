<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class EndsWithOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('ends with', 20, Associativity::LEFT);
    }
}
