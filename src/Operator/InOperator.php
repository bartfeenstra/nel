<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class InOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('in', 20, Associativity::LEFT);
    }
}
