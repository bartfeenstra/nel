<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class ContainsOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('contains', 20, Associativity::LEFT);
    }
}
