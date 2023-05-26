<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

enum TokenType
{
    case WHITESPACE;
    case STRING;
    case INTEGER;
    case OPERATOR;
}
