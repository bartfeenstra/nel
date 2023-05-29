<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\StringType;
use Bartfeenstra\Nel\Type\Type;

final class EndsWithOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'ends with',
            20,
            Associativity::LEFT,
            new StringType(),
            new StringType(),
        );
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
