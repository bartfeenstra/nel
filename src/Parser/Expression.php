<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Type\Type;

interface Expression
{
    public function type(): Type;
}
