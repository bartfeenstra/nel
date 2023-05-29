<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

final class ListType extends Type
{
    public function __construct(
        public readonly Type $itemType,
    ) {
    }

    public function phpType(): PhpType
    {
        return PhpType::ARRAY;
    }

    public function __toString(): string
    {
        return sprintf('list[%s]', $this->itemType);
    }
}
