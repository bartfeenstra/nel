<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type;

final class AndOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('and', 15, Associativity::LEFT);
    }

    public function type(): Type
    {
        return Type::BOOLEAN;
    }
}