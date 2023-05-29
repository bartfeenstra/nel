<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class WhitespaceToken extends Token implements DoNotParseToken
{
    public function __construct(
        string $source,
        int $line,
        int $column,
        public readonly string $value,
    ) {
        parent::__construct($source, $line, $column);
    }
}
