<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\Type;

final class IsGreaterThanOrEqualsOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'ge',
            new Operand(20, new IntegerType()),
            new Operand(21, new IntegerType()),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
