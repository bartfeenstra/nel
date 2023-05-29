<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Type\StructType;
use Bartfeenstra\Nel\Type\Type;

final class FieldExpression implements Expression
{
    private readonly StructType $leftOperandType;

    public function __construct(
        public readonly Expression $leftOperand,
        public readonly string $fieldName,
    ) {
        $leftOperandType = $leftOperand->type();
        if (!($leftOperandType instanceof StructType)) {
            throw new ParseError(sprintf(
                'Cannot access field "%s" on %s.',
                $this->fieldName,
                $leftOperandType,
            ));
        }
        $this->leftOperandType = $leftOperandType;
    }

    public function type(): Type
    {
        return $this->leftOperandType->fieldType($this->fieldName);
    }
}
