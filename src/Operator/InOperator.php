<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Parser\Expression;
use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\GenericType;
use Bartfeenstra\Nel\Type\ListType;
use Bartfeenstra\Nel\Type\Type;

final class InOperator extends Operator
{
    protected function __construct()
    {
        parent::__construct(
            'in',
            new Operand(20, new GenericType()),
            new Operand(21, new ListType(new GenericType())),
        );
    }

    public function validateOperands(Expression $leftOperand, Expression $rightOperand): void
    {
        $this->validateRightOperand($rightOperand);
        /** @var Expression $rightOperand */
        /** @var ListType $rightOperandType */
        $rightOperandType = $rightOperand->type();
        if ($rightOperandType->itemType != $leftOperand->type()) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its right operand to be %s, but instead it evaluates to %s.',
                $this->token,
                new ListType($leftOperand->type()),
                $rightOperandType->itemType,
            ));
        }
    }

    public function validateLeftOperand(Expression $operand): void
    {
        // We validate the left operand in $this->validateOperands().
    }

    public function validateRightOperand(Expression $operand): void
    {
        if (!($operand->type() instanceof ListType)) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its right operand to be a list, but instead it evaluates to %s.',
                $this->token,
                $operand->type(),
            ));
        }
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
