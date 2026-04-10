<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Formatador;

use DbkIrrf\Dominio\Contrato\FormatadorCampoInterface;

final class FormatadorNumerico implements FormatadorCampoInterface
{
    public function formatar(string $valor, int $tamanho): string
    {
        $valor = preg_replace('/\D/', '', $valor);

        return str_pad($valor, $tamanho, '0', STR_PAD_LEFT);
    }

    public function formatarInteiro(int $valor, int $tamanho): string
    {
        return str_pad((string) $valor, $tamanho, '0', STR_PAD_LEFT);
    }
}
