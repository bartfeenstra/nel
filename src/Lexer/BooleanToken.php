<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\BooleanExpression;

final class BooleanToken extends Token implements ExpressionFactoryToken
{
    public function __construct(
        int $cursor,
        public readonly bool $value,
    ) {
        parent::__construct($cursor);
    }

    public function expression(): BooleanExpression
    {
        return new BooleanExpression($this->value);
    }
}
