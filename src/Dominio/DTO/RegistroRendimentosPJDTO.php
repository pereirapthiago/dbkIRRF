<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 21 - Rendimentos Tributaveis Recebidos de PJ - 170 caracteres.
 *
 * ATENCAO: A ordem no arquivo difere da tela do programa IRPF.
 * Arquivo: Rendimentos > Contrib Prev > 13o Salario > IR Retido Fonte
 * Programa: Rendimentos > Contrib Prev > IR Retido > 13o Salario
 */
final readonly class RegistroRendimentosPJDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public Cnpj $cnpjFontePagadora,
        public string $nomeFontePagadora = '',
        public ValorMonetario $rendimentosRecebidos = new ValorMonetario(0),
        public ValorMonetario $contribPrevidenciaria = new ValorMonetario(0),
        public ValorMonetario $decimoTerceiroSalario = new ValorMonetario(0),
        public ValorMonetario $impostoRetidoFonte = new ValorMonetario(0),
        public ValorMonetario $irrfDecimoTerceiro = new ValorMonetario(0),
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTOS_PJ;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('cnpjFontePagadora', 14, 14, TipoCampo::NUMERICO),
            new CampoDTO('nomeFontePagadora', 28, 60, TipoCampo::ALFA),
            new CampoDTO('rendimentosRecebidos', 88, 13, TipoCampo::NUMERICO, descricao: 'Rendimentos recebidos (centavos)'),
            new CampoDTO('contribPrevidenciaria', 101, 13, TipoCampo::NUMERICO, descricao: 'Contrib previdenciaria (centavos)'),
            new CampoDTO('decimoTerceiroSalario', 114, 13, TipoCampo::NUMERICO, descricao: '13o salario (centavos)'),
            new CampoDTO('impostoRetidoFonte', 127, 13, TipoCampo::NUMERICO, descricao: 'Imposto retido fonte (centavos)'),
            new CampoDTO('reservado', 140, 8, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('irrfDecimoTerceiro', 148, 13, TipoCampo::NUMERICO, descricao: 'IRRF sobre 13o salario (centavos)'),
            new CampoDTO('checksum', 161, 10, TipoCampo::NUMERICO),
        );
    }
}
