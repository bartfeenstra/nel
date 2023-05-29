<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type\AnyType;
use Bartfeenstra\Nel\Type\ListType;
use Bartfeenstra\Nel\Type\Type;

final class ListExpression implements Expression
{
    private Type $itemType;

    /**
     * @param list<Expression> $values
     */
    public function __construct(
        public readonly array $values,
    ) {
        $this->itemType = $values ? $values[0]->type() : new AnyType();
    }

    public function type(): ListType
    {
        return new ListType($this->itemType);
    }
}
