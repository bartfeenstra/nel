<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

final class IntegerType extends Type
{
    public function phpType(): PhpType
    {
        return PhpType::INTEGER;
    }
}
