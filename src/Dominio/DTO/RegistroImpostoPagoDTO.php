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
 * Registro 23 - Imposto Pago / Retido por Codigo - 40 caracteres.
 */
final readonly class RegistroImpostoPagoDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public string $codigo = '0001',
        public ValorMonetario $valor = new ValorMonetario(0),
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::IMPOSTO_PAGO;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO, descricao: 'Tipo registro'),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO, descricao: 'CPF contribuinte'),
            new CampoDTO('codigo', 14, 4, TipoCampo::NUMERICO, descricao: 'Codigo/sequencial'),
            new CampoDTO('valor', 18, 13, TipoCampo::NUMERICO, descricao: 'Valor em centavos'),
            new CampoDTO('checksum', 31, 10, TipoCampo::NUMERICO, descricao: 'Checksum'),
        );
    }
}
