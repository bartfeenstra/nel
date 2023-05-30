<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\StringType;
use Bartfeenstra\Nel\Type\Type;

final class EndsWithOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'ends with',
            new Operand(20, new StringType()),
            new Operand(21, new StringType()),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
