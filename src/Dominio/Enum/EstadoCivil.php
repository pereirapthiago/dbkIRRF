<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Enum;

enum EstadoCivil: string
{
    case SOLTEIRO = 'S';
    case CASADO = 'C';
    case DIVORCIADO = 'D';
    case VIUVO = 'V';
    case SEPARADO_JUDICIALMENTE = 'J';
}
