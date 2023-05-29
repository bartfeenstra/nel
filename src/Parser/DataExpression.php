<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type;

final class DataExpression implements Expression
{
    /**
     * @param list<string> $fields
     */
    public function __construct(
        public readonly array $fields,
    ) {
    }

    public function type(): Type
    {
        // @todo Revisit this. This depends on the input data...
        return Type::NULL;
    }
}
