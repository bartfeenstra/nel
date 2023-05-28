<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type;

final class SubtractOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('subtract', 30, Associativity::LEFT);
    }

    public function type(): Type
    {
        return Type::INTEGER;
    }
}
