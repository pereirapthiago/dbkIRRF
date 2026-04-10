<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Formatador;

use DbkIrrf\Dominio\Contrato\FormatadorCampoInterface;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class FormatadorMonetario implements FormatadorCampoInterface
{
    private const TAMANHO_PADRAO = 13;

    public function formatar(string $valor, int $tamanho): string
    {
        $valor = preg_replace('/\D/', '', $valor);

        return str_pad($valor, $tamanho, '0', STR_PAD_LEFT);
    }

    public function formatarValor(ValorMonetario $valor, int $tamanho = self::TAMANHO_PADRAO): string
    {
        return $valor->formatar($tamanho);
    }

    public function formatarCentavos(int $centavos, int $tamanho = self::TAMANHO_PADRAO): string
    {
        return str_pad((string) $centavos, $tamanho, '0', STR_PAD_LEFT);
    }
}
