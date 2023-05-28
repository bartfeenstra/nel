<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel;

final class SyntaxError extends \RuntimeException
{
    public function __construct(
        public readonly string $tokenValue,
        public readonly int $cursor,
        public readonly string $source
    ) {
        parent::__construct(sprintf(
            'Unknown token "%s" at position %d of "%s".',
            $this->tokenValue,
            $this->cursor,
            $this->source,
        ));
    }
}
