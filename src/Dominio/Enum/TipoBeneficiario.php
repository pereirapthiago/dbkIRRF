<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Enum;

enum TipoBeneficiario: string
{
    case TITULAR = 'T';
    case DEPENDENTE = 'D';
}
