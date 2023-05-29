<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Operator\Operator;
use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Type\BooleanType;
use Bartfeenstra\Nel\Type\Type;

final class UnaryOperatorExpression extends OperatorExpression
{
    public function __construct(
        Operator $operator,
        public readonly Expression $operand,
    ) {
        parent::__construct($operator);
        if (!($this->operand->type() instanceof BooleanType)) {
            throw new ParseError(sprintf(
                'Operator "%s" expects its operand to yield a boolean, but instead it yields %s.',
                $this->operator->token,
                $this->operand->type(),
            ));
        }
    }

    public function type(): Type
    {
        return new BooleanType();
    }
}
