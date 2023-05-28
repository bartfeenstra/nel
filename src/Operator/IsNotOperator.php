<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class IsNotOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('is not', 20, Associativity::LEFT);
    }
}
