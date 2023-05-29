<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

final class BooleanType extends Type
{
    public function phpType(): PhpType
    {
        return PhpType::BOOLEAN;
    }
}
