<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class IsGreaterThanOrEqualsOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('ge', 20, Associativity::LEFT);
    }
}
