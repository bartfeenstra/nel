<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class StartsWithOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('starts with', 20, Associativity::LEFT);
    }
}
