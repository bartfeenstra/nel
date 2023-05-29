<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class DataFieldToken extends Token
{
    public function __construct(
        int $cursor,
        public readonly string $field,
    ) {
        parent::__construct($cursor);
    }
}
