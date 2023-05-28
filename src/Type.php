<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel;

enum Type: string
{
    case INTEGER = 'integer';
    case STRING = 'string';
    case NULL = 'null';
    case BOOLEAN = 'boolean';
}
