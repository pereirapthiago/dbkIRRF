<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 24 - Deducoes Legais por Codigo - 40 caracteres.
 *
 * Codigos conhecidos:
 * - 0001: Previdencia oficial
 * - 0006: Tributacao exclusiva/definitiva
 * - 0007: Rendimentos recebidos acumuladamente (RRA)
 */
final readonly class RegistroDeducaoLegalDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public string $codigoDeducao = '0001',
        public ValorMonetario $valor = new ValorMonetario(0),
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::DEDUCAO_LEGAL;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('codigoDeducao', 14, 4, TipoCampo::NUMERICO, descricao: 'Codigo deducao (0001/0006/0007)'),
            new CampoDTO('valor', 18, 13, TipoCampo::NUMERICO),
            new CampoDTO('checksum', 31, 10, TipoCampo::NUMERICO),
        );
    }
}
