<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Contrato;

use DbkIrrf\Dominio\Enum\TipoRegistro;

interface LeitorRegistroInterface
{
    public function ler(string $linha): RegistroInterface;

    public function suportaTipo(): TipoRegistro;
}
