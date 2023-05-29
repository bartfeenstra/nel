<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

class StructType extends Type
{
    /**
     * @param array<string, Type> $fields
     */
    public function __construct(
        public readonly string $typeName,
        public readonly array $fields,
    ) {
    }

    public function phpType(): PhpType
    {
        return PhpType::OBJECT;
    }

    public function fieldType(string $field): Type
    {
        return $this->fields[$field];
    }

    /**
     * @return array<string, Type> $fields
     */
    public function fields(): array
    {
        return $this->fields;
    }

    public function __toString(): string
    {
        return 'struct:' . $this->typeName;
    }
}
