<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Contrato;

use DbkIrrf\Dominio\Enum\TipoRegistro;

interface GeradorRegistroInterface
{
    public function gerar(RegistroInterface $registro): string;

    public function suportaTipo(): TipoRegistro;
}
