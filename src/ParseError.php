<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel;

use Bartfeenstra\Nel\Lexer\Token;
use RuntimeException;

final class ParseError extends RuntimeException
{
    public function __construct(public readonly ?Token $token, string $message)
    {
        if ($token) {
            $message .= sprintf("\nOn line %d, at column %d of:\n`%s`", $token->line, $token->column, $token->source);
        }
        parent::__construct($message);
    }
}
