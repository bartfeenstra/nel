<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

final class GenericType extends Type
{
    public function phpType(): PhpType
    {
        return PhpType::MIXED;
    }
}
