<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\NullExpression;

final class NullToken extends Token implements ExpressionFactoryToken
{
    public function expression(): NullExpression
    {
        return new NullExpression();
    }
}
