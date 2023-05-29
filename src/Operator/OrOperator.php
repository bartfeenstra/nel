<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class OrOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'or',
            10,
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
