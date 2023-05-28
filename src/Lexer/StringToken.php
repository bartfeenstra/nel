<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\StringExpression;

final class StringToken extends Token implements ExpressionFactoryToken
{
    public function __construct(
        int $cursor,
        public readonly string $value,
    ) {
        parent::__construct($cursor);
    }

    public function expression(): StringExpression
    {
        return new StringExpression($this->value);
    }
}
