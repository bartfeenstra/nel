<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\Parser\Expression;

interface ExpressionFactoryToken
{
    public function expression(): Expression;
}
