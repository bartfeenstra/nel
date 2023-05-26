<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

enum TokenType
{
    case WHITESPACE;
    case LITERAL_STRING;
    case LITERAL_INTEGER;
}
