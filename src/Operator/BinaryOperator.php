<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

abstract class BinaryOperator extends Operator
{
    protected function __construct(
        string $token,
        int $precedence,
        public readonly Associativity $associativity,
    ) {
        parent::__construct($token, $precedence);
    }
}
