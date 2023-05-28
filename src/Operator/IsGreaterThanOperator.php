<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class IsGreaterThanOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('gt', 20, Associativity::LEFT);
    }
}
