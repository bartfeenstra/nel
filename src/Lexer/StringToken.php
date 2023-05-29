<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\StringExpression;

final class StringToken extends Token implements ExpressionFactoryToken
{
    public function __construct(
        string $source,
        int $line,
        int $column,
        public readonly string $value,
    ) {
        parent::__construct($source, $line, $column);
    }

    public function expression(): StringExpression
    {
        return new StringExpression($this->value);
    }
}
