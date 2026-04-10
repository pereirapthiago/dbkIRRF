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
 * Registro 26 - Pagamentos Efetuados - 671 caracteres.
 */
final readonly class RegistroPagamentoDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public string $codigoPagamento = '00',
        public string $cpfCnpjBeneficiario = '',
        public string $nomeBeneficiario = '',
        public ValorMonetario $valorPago = new ValorMonetario(0),
        public ValorMonetario $parcelaNaoDedutivel = new ValorMonetario(0),
        public string $sequencial = '1',
        public string $flagTitularDependente = 'T',
        public string $descricao = '',
        public string $codigoPais = '000',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::PAGAMENTO;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('codigoPagamento', 14, 2, TipoCampo::NUMERICO),
            new CampoDTO('reservado1', 16, 5, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('cpfCnpjBeneficiario', 21, 14, TipoCampo::ALFA),
            new CampoDTO('nomeBeneficiario', 35, 60, TipoCampo::ALFA),
            new CampoDTO('reservado2', 95, 11, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('valorPago', 106, 13, TipoCampo::NUMERICO, descricao: 'Valor pago (centavos)'),
            new CampoDTO('parcelaNaoDedutivel', 119, 13, TipoCampo::NUMERICO, descricao: 'Parcela nao dedutivel/reembolso (centavos)'),
            new CampoDTO('reservado3', 132, 13, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('sequencial', 145, 1, TipoCampo::NUMERICO),
            new CampoDTO('flagTitularDependente', 146, 1, TipoCampo::ALFA, descricao: 'T=titular'),
            new CampoDTO('descricao', 147, 512, TipoCampo::ALFA, descricao: 'Descricao/historico'),
            new CampoDTO('codigoPais', 659, 3, TipoCampo::NUMERICO, descricao: 'Codigo pais'),
            new CampoDTO('checksum', 662, 10, TipoCampo::NUMERICO),
        );
    }
}
