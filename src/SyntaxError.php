<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel;

use RuntimeException;

final class SyntaxError extends RuntimeException
{
    public function __construct(
        public readonly string $source,
        public readonly int $sourceLine,
        public readonly int $sourceColumn,
        public readonly string $tokenValue,
    ) {
        parent::__construct(sprintf(
            "Unexpected character \"%s\" on line %d, at column %d of:\n`%s`",
            $tokenValue,
            $sourceLine,
            $sourceColumn,
            $source,
        ));
    }
}
