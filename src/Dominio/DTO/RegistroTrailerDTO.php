<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;

/**
 * Registro T9 - Trailer/Totalizador - 449 caracteres.
 * Ultimo registro do arquivo DBK.
 */
final readonly class RegistroTrailerDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public int $totalRegistros = 0,
        public string $contadoresRaw = '',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::TRAILER;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::ALFA, descricao: 'Tipo registro T9'),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('totalRegistros', 14, 8, TipoCampo::NUMERICO, descricao: 'Total de registros'),
            new CampoDTO('contadoresRaw', 22, 418, TipoCampo::NUMERICO, descricao: 'Contadores por tipo registro'),
            new CampoDTO('checksum', 440, 10, TipoCampo::NUMERICO),
        );
    }
}
