<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Contrato;

use DbkIrrf\Dominio\Enum\TipoRegistro;

interface RegistroInterface
{
    public function obterTipo(): TipoRegistro;
}
