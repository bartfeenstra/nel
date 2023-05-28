<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type;

final class NotOperator extends UnaryOperator
{
    protected function __construct()
    {
        parent::__construct('not', 50);
    }

    public function type(): Type
    {
        return Type::BOOLEAN;
    }
}
