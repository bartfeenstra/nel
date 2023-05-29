<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Operator\Operator;

final class OperatorToken extends Token
{
    public function __construct(
        string $source,
        int $line,
        int $column,
        public readonly Operator $operator,
    ) {
        parent::__construct($source, $line, $column);
    }
}
