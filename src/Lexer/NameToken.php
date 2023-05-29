<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class NameToken extends Token
{
    public function __construct(
        string $source,
        int $line,
        int $column,
        public readonly string $name,
    ) {
        parent::__construct($source, $line, $column);
    }
}
