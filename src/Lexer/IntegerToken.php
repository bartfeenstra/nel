<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\IntegerExpression;

final class IntegerToken extends Token implements ExpressionFactoryToken
{
    public function __construct(
        int $cursor,
        public readonly int $value,
    ) {
        parent::__construct($cursor);
    }

    public function expression(): IntegerExpression
    {
        return new IntegerExpression($this->value);
    }
}
