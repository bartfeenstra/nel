<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

abstract class Token
{
    public function __construct(
        public readonly string $source,
        public readonly int $line,
        public readonly int $column,
    ) {
    }
}
