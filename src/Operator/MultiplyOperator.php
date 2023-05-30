<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\Type;

final class MultiplyOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'mul',
            new Operand(60, new IntegerType()),
            new Operand(61, new IntegerType()),
        );
    }

    public function type(): Type
    {
        return new IntegerType();
    }
}
