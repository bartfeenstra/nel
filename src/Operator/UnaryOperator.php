<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\Type;

abstract class UnaryOperator extends Operator
{
    public function __construct(
        string $token,
        int $precedence,
        public readonly Type $operandType,
    ) {
        parent::__construct($token, $precedence);
    }
}
