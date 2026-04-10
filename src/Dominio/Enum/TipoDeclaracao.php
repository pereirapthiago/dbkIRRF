<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Enum;

enum TipoDeclaracao: string
{
    case ORIGINAL = '0';
    case RETIFICADORA = '1';

    public function obterSufixoArquivo(): string
    {
        return match ($this) {
            self::ORIGINAL => 'ORIGI',
            self::RETIFICADORA => 'RETIF',
        };
    }
}
