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

final class InOperator extends BinaryOperator
{
    protected function __construct()
    {
        parent::__construct(
            'in',
            20,
            Associativity::LEFT,
            new GenericType(),
            new ListType(new GenericType()),
        );
    }

    public function validateOperands(Expression $leftOperand, Expression $rightOperand): void
    {
        $expectedListItemType = $leftOperand->type();
        $expectedListType = new ListType($expectedListItemType);
        $actualListType = $rightOperand->type();
        if (!($rightOperand instanceof ListExpression) or $actualListType != $expectedListType) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its right operand to be %s, but instead it evaluates to %s.',
                $this->token,
                $expectedListType,
                $actualListType,
            ));
        }
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
