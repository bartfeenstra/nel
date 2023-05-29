<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Parser\Expression;
use Bartfeenstra\Nel\Parser\ListExpression;
use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\GenericType;
use Bartfeenstra\Nel\Type\ListType;
use Bartfeenstra\Nel\Type\Type;

final class ContainsOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'contains',
            20,
            Associativity::LEFT,
            new ListType(new GenericType()),
            new GenericType(),
        );
    }

    public function validateOperands(Expression $leftOperand, Expression $rightOperand): void
    {
        if (!($leftOperand instanceof ListExpression)) {
            throw new ParseError(sprintf(
                'Operator "%s" expects its left operand to be a list, but instead it evaluates to %s.',
                $this->token,
                $leftOperand->type(),
            ));
        }
        if ($leftOperand->type()->itemType != $rightOperand->type()) {
            throw new ParseError(sprintf(
                'Operator "%s" expects its left operand to be %s, but instead it evaluates to %s.',
                $this->token,
                new ListType($rightOperand->type()),
                $leftOperand->type()->itemType,
            ));
        }
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
