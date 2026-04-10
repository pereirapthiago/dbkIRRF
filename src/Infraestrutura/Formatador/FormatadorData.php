<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Formatador;

use DbkIrrf\Dominio\Contrato\FormatadorCampoInterface;
use DbkIrrf\Dominio\ValorObjeto\Data;

final class FormatadorData implements FormatadorCampoInterface
{
    public function formatar(string $valor, int $tamanho): string
    {
        return str_pad($valor, $tamanho, ' ', STR_PAD_RIGHT);
    }

    public function formatarData(Data $data): string
    {
        return (string) $data;
    }

    public function formatarDateTime(\DateTimeInterface $data): string
    {
        return $data->format('dmY');
    }
}
