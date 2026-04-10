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
 * Registro 22 - Rendimentos Mensais PF/Exterior - 167 caracteres.
 * Sao 12 registros por declaracao (janeiro a dezembro).
 */
final readonly class RegistroRendimentosMensaisDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public int $mesReferencia,
        public ValorMonetario $rendNaoAssalariado = new ValorMonetario(0),
        public ValorMonetario $temporada = new ValorMonetario(0),
        public ValorMonetario $outrosRendimentos = new ValorMonetario(0),
        public ValorMonetario $exterior = new ValorMonetario(0),
        public ValorMonetario $previdencia = new ValorMonetario(0),
        public ValorMonetario $dependentes = new ValorMonetario(0),
        public ValorMonetario $pensaoAlimenticia = new ValorMonetario(0),
        public ValorMonetario $livroCaixa = new ValorMonetario(0),
        public ValorMonetario $totalRendimentosMes = new ValorMonetario(0),
        public ValorMonetario $darfPago = new ValorMonetario(0),
        public string $flagNS = 'N',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTOS_MENSAIS;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('flagNS', 14, 1, TipoCampo::ALFA),
            new CampoDTO('reservado', 15, 11, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('mesReferencia', 26, 2, TipoCampo::NUMERICO, descricao: 'Mes referencia (01-12)'),
            new CampoDTO('rendNaoAssalariado', 28, 13, TipoCampo::NUMERICO),
            new CampoDTO('temporada', 41, 13, TipoCampo::NUMERICO, descricao: 'Aliquota incl temporada (centavos)'),
            new CampoDTO('outrosRendimentos', 54, 13, TipoCampo::NUMERICO),
            new CampoDTO('exterior', 67, 13, TipoCampo::NUMERICO, descricao: 'Rendimentos exterior (centavos)'),
            new CampoDTO('previdencia', 80, 13, TipoCampo::NUMERICO),
            new CampoDTO('dependentes', 93, 13, TipoCampo::NUMERICO, descricao: 'Qtd/valor dependentes'),
            new CampoDTO('pensaoAlimenticia', 106, 13, TipoCampo::NUMERICO),
            new CampoDTO('livroCaixa', 119, 13, TipoCampo::NUMERICO),
            new CampoDTO('totalRendimentosMes', 132, 13, TipoCampo::NUMERICO, descricao: 'Total rendimentos mes (centavos)'),
            new CampoDTO('darfPago', 145, 13, TipoCampo::NUMERICO, descricao: 'Darf pago cod 0190 (centavos)'),
            new CampoDTO('checksum', 158, 10, TipoCampo::NUMERICO),
        );
    }
}
