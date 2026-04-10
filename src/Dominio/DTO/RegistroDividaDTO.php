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
 * Registro 28 - Dividas e Onus Reais - 576 caracteres.
 */
final readonly class RegistroDividaDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public string $codigoDivida = '11',
        public string $descricao = '',
        public ValorMonetario $saldoAnterior = new ValorMonetario(0),
        public ValorMonetario $saldoAtual = new ValorMonetario(0),
        public ValorMonetario $valorPagoAno = new ValorMonetario(0),
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::DIVIDA;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('codigoDivida', 14, 2, TipoCampo::NUMERICO, descricao: 'Codigo divida'),
            new CampoDTO('descricao', 16, 500, TipoCampo::ALFA, descricao: 'Descricao/discriminacao'),
            new CampoDTO('reservado', 516, 12, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('saldoAnterior', 528, 13, TipoCampo::NUMERICO, descricao: 'Saldo em 31/12 anterior (centavos)'),
            new CampoDTO('saldoAtual', 541, 13, TipoCampo::NUMERICO, descricao: 'Saldo em 31/12 atual (centavos)'),
            new CampoDTO('valorPagoAno', 554, 13, TipoCampo::NUMERICO, descricao: 'Valor pago no ano (centavos)'),
            new CampoDTO('checksum', 567, 10, TipoCampo::NUMERICO),
        );
    }
}
