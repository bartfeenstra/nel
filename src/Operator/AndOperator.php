<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class AndOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'and',
            new Operand(15, new BooleanType()),
            new Operand(16, new BooleanType()),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
