<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\Type;

final class AddOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'add',
            new Operand(30, new IntegerType()),
            new Operand(31, new IntegerType()),
        );
    }

    public function type(): Type
    {
        return new IntegerType();
    }
}
