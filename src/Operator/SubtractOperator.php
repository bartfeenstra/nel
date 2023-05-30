<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\Type;

final class SubtractOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'sub',
            new Operand(30, new IntegerType()),
            new Operand(31, new IntegerType()),
        );
    }

    public function type(): Type
    {
        return new IntegerType();
    }
}
