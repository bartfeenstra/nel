<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class NameToken extends Token
{
    public function __construct(
        int $cursor,
        public readonly string $name,
    ) {
        parent::__construct($cursor);
    }
}
