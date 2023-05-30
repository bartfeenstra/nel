<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Parser\Expression;
use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\GenericType;
use Bartfeenstra\Nel\Type\ListType;
use Bartfeenstra\Nel\Type\Type;

final class ContainsOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'contains',
            new Operand(20, new ListType(new GenericType())),
            new Operand(21, new GenericType()),
        );
    }

    public function validateOperands(Expression $leftOperand, Expression $rightOperand): void
    {
        $this->validateLeftOperand($leftOperand);
        /** @var Expression $leftOperand */
        /** @var ListType $leftOperandType */
        $leftOperandType = $leftOperand->type();
        if ($leftOperandType->itemType != $rightOperand->type()) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its left operand to be %s, but instead it evaluates to %s.',
                $this->token,
                new ListType($rightOperand->type()),
                $leftOperandType->itemType,
            ));
        }
    }

    public function validateLeftOperand(Expression $operand): void
    {
        if (!($operand->type() instanceof ListType)) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its left operand to be a list, but instead it evaluates to %s.',
                $this->token,
                $operand->type(),
            ));
        }
    }

    public function validateRightOperand(Expression $operand): void
    {
        // We validate the right operand in $this->validateOperands().
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
