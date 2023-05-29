<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Operator\BinaryOperator;
use Bartfeenstra\Nel\Type\Type;

final class BinaryOperatorExpression extends OperatorExpression
{
    public function __construct(
        BinaryOperator $operator,
        public readonly Expression $leftOperand,
        public readonly Expression $rightOperand,
    ) {
        parent::__construct($operator);
        $operator->validateOperands($leftOperand, $rightOperand);
    }

    public function type(): Type
    {
        return $this->operator->type();
    }
}
