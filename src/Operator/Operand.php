<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type\Type;

final class Operand
{
    public function __construct(
        public readonly int $precedence,
        public readonly Type $type,
    ) {
    }
}
