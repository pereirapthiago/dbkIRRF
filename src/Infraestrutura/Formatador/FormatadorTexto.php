<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Formatador;

use DbkIrrf\Dominio\Contrato\FormatadorCampoInterface;

final class FormatadorTexto implements FormatadorCampoInterface
{
    private const MAPA_ACENTOS = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'Ç' => 'C', 'Ñ' => 'N',
        'à' => 'A', 'á' => 'A', 'â' => 'A', 'ã' => 'A', 'ä' => 'A',
        'è' => 'E', 'é' => 'E', 'ê' => 'E', 'ë' => 'E',
        'ì' => 'I', 'í' => 'I', 'î' => 'I', 'ï' => 'I',
        'ò' => 'O', 'ó' => 'O', 'ô' => 'O', 'õ' => 'O', 'ö' => 'O',
        'ù' => 'U', 'ú' => 'U', 'û' => 'U', 'ü' => 'U',
        'ç' => 'C', 'ñ' => 'N',
    ];

    public function formatar(string $valor, int $tamanho): string
    {
        $valor = strtr($valor, self::MAPA_ACENTOS);
        $valor = mb_strtoupper($valor, 'UTF-8');
        $valor = str_pad($valor, $tamanho, ' ', STR_PAD_RIGHT);

        return substr($valor, 0, $tamanho);
    }
}
