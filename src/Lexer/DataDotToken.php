<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class DataDotToken extends Token
{
    public function __construct(
        int $cursor,
    ) {
        parent::__construct($cursor);
    }
}
