<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type;

final class ListExpression implements Expression
{
    /**
     * @param list<mixed> $values
     */
    public function __construct(
        public readonly array $values,
    ) {
    }

    public function type(): Type
    {
        // @todo Revisit this.
        return Type::NULL;
    }
}
