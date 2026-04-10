<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Contrato;

interface FormatadorCampoInterface
{
    public function formatar(string $valor, int $tamanho): string;
}
