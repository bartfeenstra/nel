<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Operator\Operator;
use Bartfeenstra\Nel\Type\Type;
use http\Exception\RuntimeException;

final class OperatorExpression implements Expression
{
    public function __construct(
        public readonly Operator $operator,
        public readonly ?Expression $leftOperand,
        public readonly ?Expression $rightOperand = null,
    ) {
        if ($this->operator->isInfix()) {
            if (!$leftOperand or !$rightOperand) {
                throw new \RuntimeException('Both operands are required for infix operators.');
            }
            $operator->validateOperands($leftOperand, $rightOperand);
        } else {
            if ($this->leftOperand) {
                $operator->validateLeftOperand($this->leftOperand);
            } elseif ($this->rightOperand) {
                $operator->validateRightOperand($this->rightOperand);
            }
        }
    }

    public function type(): Type
    {
        return $this->operator->type();
    }
}
