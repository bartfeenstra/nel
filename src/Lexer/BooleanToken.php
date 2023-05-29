<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\BooleanExpression;

final class BooleanToken extends Token implements ExpressionFactoryToken
{
    public function __construct(
        string $source,
        int $line,
        int $column,
        public readonly bool $value,
    ) {
        parent::__construct($source, $line, $column);
    }

    public function expression(): BooleanExpression
    {
        return new BooleanExpression($this->value);
    }
}
