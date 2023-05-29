<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\AnyType;
use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class IsOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'is',
            20,
            Associativity::LEFT,
            new AnyType(),
            new AnyType(),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
