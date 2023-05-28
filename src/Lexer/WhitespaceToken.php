<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class WhitespaceToken extends Token implements DoNotParseToken
{
    public function __construct(
        int $cursor,
        public readonly string $value,
    ) {
        parent::__construct($cursor);
    }
}
