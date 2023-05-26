<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class Token
{
    public function __construct(
        public readonly TokenType $type,
        public readonly string|int|null $value,
        public readonly int $cursor,
    ) {
    }
}
