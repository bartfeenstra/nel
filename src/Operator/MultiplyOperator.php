<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\Type;

final class MultiplyOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'mul',
            60,
            Associativity::LEFT,
            new IntegerType(),
            new IntegerType(),
        );
    }

    public function type(): Type
    {
        return new IntegerType();
    }
}
