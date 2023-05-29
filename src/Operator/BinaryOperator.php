<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Parser\Expression;
use Bartfeenstra\Nel\Type\Type;

abstract class BinaryOperator extends Operator
{
    protected function __construct(
        string $token,
        int $precedence,
        public readonly Associativity $associativity,
        public readonly Type $leftOperandType,
        public readonly Type $rightOperandType,
    ) {
        parent::__construct($token, $precedence);
    }

    public function validateOperands(Expression $leftOperand, Expression $rightOperand): void
    {
        if ($leftOperand->type() != ($this->leftOperandType)) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its left operand to be %s, but instead it evaluates to %s.',
                $this->token,
                $this->leftOperandType,
                $leftOperand->type(),
            ));
        }
        if ($rightOperand->type() != ($this->rightOperandType)) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its right operand to be %s, but instead it evaluates to %s.',
                $this->token,
                $this->rightOperandType,
                $rightOperand->type(),
            ));
        }
    }
}
