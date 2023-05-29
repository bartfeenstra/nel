<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class NotOperator extends UnaryOperator
{
    protected function __construct()
    {
        parent::__construct('not', 50, new BooleanType());
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
