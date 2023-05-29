<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class AndOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'and',
            15,
            Associativity::LEFT,
            new BooleanType(),
            new BooleanType(),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
