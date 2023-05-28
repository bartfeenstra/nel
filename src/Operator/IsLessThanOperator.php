<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type;

final class IsLessThanOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct('lt', 20, Associativity::LEFT);
    }

    public function type(): Type
    {
        return Type::BOOLEAN;
    }
}
