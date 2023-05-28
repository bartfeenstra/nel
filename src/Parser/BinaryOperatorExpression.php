<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Operator\Operator;
use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Type;

final class BinaryOperatorExpression extends OperatorExpression
{
    public function __construct(
        Operator $operator,
        public readonly Expression $leftOperand,
        public readonly Expression $rightOperand,
    ) {
        parent::__construct($operator);
        if ($this->leftOperand->type() !== $this->rightOperand->type()) {
            throw new ParseError(sprintf(
                'Operator "%s" expects its operands to be of the same type, but instead they yield %s and %s.',
                $this->operator->value,
                $this->leftOperand->type()->value,
                $this->rightOperand->type()->value,
            ));
        }
    }

    public function type(): Type
    {
        return Type::BOOLEAN;
    }
}
