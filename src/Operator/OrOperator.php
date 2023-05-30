<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class OrOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'or',
            new Operand(10, new BooleanType()),
            new Operand(11, new BooleanType()),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
