<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

final class NullType extends Type
{
    public function phpType(): PhpType
    {
        return PhpType::NULL;
    }
}
