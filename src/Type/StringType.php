<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

final class StringType extends Type
{
    public function phpType(): PhpType
    {
        return PhpType::STRING;
    }
}
