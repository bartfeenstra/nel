<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\Type;

final class IsLessThanOrEqualsOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'le',
            new Operand(20, new IntegerType()),
            new Operand(21, new IntegerType()),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
