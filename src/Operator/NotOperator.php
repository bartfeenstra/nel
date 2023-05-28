<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

final class NotOperator extends UnaryOperator
{
    protected function __construct()
    {
        parent::__construct('not', 50);
    }
}
