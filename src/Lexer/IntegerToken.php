<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\IntegerExpression;

final class IntegerToken extends Token implements ExpressionFactoryToken
{
    public function __construct(
        string $source,
        int $line,
        int $column,
        public readonly int $value,
    ) {
        parent::__construct($source, $line, $column);
    }

    public function expression(): IntegerExpression
    {
        return new IntegerExpression($this->value);
    }
}
