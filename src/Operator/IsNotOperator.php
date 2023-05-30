<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\AnyType;
use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class IsNotOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'is not',
            new Operand(20, new AnyType()),
            new Operand(21, new AnyType()),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
