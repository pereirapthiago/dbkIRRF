<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 37 - Detalhe Investimento Exterior (Lei 14.754/2023) - 103 caracteres.
 * Cada bem de investimento exterior gera 2 registros (sub-tipo 1 e 2).
 * FK para Registro 27 via idBem.
 */
final readonly class RegistroInvestExteriorDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public string $idBem = '00001',
        public string $sequencialDetalhe = '00001',
        public SubTipoInvestimento $subTipo = SubTipoInvestimento::APLICACOES_FINANCEIRAS,
        public ValorMonetario $rendimentoValor = new ValorMonetario(0),
        public ValorMonetario $impostoDevido15 = new ValorMonetario(0),
        public ValorMonetario $impostoPagoExterior = new ValorMonetario(0),
        public ValorMonetario $campoMonetario4 = new ValorMonetario(0),
        public ValorMonetario $campoMonetario5 = new ValorMonetario(0),
        public string $grupoBem = '07',
        public string $codigoItem = '99',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::INVESTIMENTO_EXTERIOR;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('idBem', 14, 5, TipoCampo::NUMERICO, descricao: 'ID do bem (FK Reg 27 pos 892-896)'),
            new CampoDTO('sequencialDetalhe', 19, 5, TipoCampo::NUMERICO, descricao: 'Sequencial do detalhe'),
            new CampoDTO('subTipo', 24, 1, TipoCampo::NUMERICO, descricao: '1=Aplicacoes Financeiras, 2=Lucros/Dividendos'),
            new CampoDTO('rendimentoValor', 25, 13, TipoCampo::NUMERICO, descricao: 'Rendimento/Valor (centavos)'),
            new CampoDTO('impostoDevido15', 38, 13, TipoCampo::NUMERICO, descricao: 'Imposto devido 15% (centavos)'),
            new CampoDTO('impostoPagoExterior', 51, 13, TipoCampo::NUMERICO, descricao: 'Imposto pago exterior (centavos)'),
            new CampoDTO('campoMonetario4', 64, 13, TipoCampo::NUMERICO),
            new CampoDTO('campoMonetario5', 77, 13, TipoCampo::NUMERICO),
            new CampoDTO('grupoBem', 90, 2, TipoCampo::NUMERICO, descricao: 'Grupo do bem (ref)'),
            new CampoDTO('codigoItem', 92, 2, TipoCampo::NUMERICO, descricao: 'Codigo item (ref)'),
            new CampoDTO('checksum', 94, 10, TipoCampo::NUMERICO),
        );
    }
}
