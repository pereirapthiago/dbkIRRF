<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Enum;

enum SubTipoInvestimento: string
{
    case APLICACOES_FINANCEIRAS = '1';
    case LUCROS_DIVIDENDOS = '2';
}
