<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Enum;

enum TipoCampo: string
{
    case ALFA = 'Alfa';
    case NUMERICO = 'Num';
    case DATA = 'Data';
    case ALFANUMERICO = 'Alfnum';
}
