<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Contrato;

interface ValidadorCampoInterface
{
    public function validar(string $valor, int $tamanhoEsperado): bool;
}
