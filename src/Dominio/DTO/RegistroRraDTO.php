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
 * Registro 45 - Rendimentos Recebidos Acumuladamente (RRA) - 216 caracteres.
 * Um registro por fonte pagadora. Aparece quando o usuario informa RRA no programa IRPF.
 *
 * ATENCAO - posicoes criticas:
 * - Pos 90-102: ZEROS obrigatorios (nao sao rendimentos)
 * - Pos 168-180: rendimentos tributaveis RRA (posicao correta)
 * - Pos 181-193: copia de pos 168-180
 * - Pos 155-167: imposto bruto RRA (zero quando abaixo da faixa tributavel)
 */
final readonly class RegistroRraDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public Cnpj $cnpjFontePagadora,
        public string $nomeFontePagadora = '',
        public ValorMonetario $contribPrevidenciaria = new ValorMonetario(0),
        public ValorMonetario $parcelaIsenta65Anos = new ValorMonetario(0),
        public ValorMonetario $impostoRetidoFonte = new ValorMonetario(0),
        public string $mesRecebimentoRRA = '01',
        public string $metadadosRRA = '00001',
        public string $flagCodigo150 = '100',
        public string $numMesesRRA = '0',
        public ValorMonetario $impostoBrutoRRA = new ValorMonetario(0),
        public ValorMonetario $rendimentosRRA = new ValorMonetario(0),
        public ValorMonetario $rendimentosRRACopia = new ValorMonetario(0),
        public ValorMonetario $campoDesconhecido194 = new ValorMonetario(0),
        // Campos internos
        public string $reservadoPos154 = '0',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::RRA;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('reservado1', 14, 2, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('cnpjFontePagadora', 16, 14, TipoCampo::NUMERICO),
            new CampoDTO('nomeFontePagadora', 30, 60, TipoCampo::ALFA),
            new CampoDTO('zeros90', 90, 13, TipoCampo::NUMERICO, obrigatorio: false, descricao: 'Zeros obrigatorios — nao sao rendimentos'),
            new CampoDTO('contribPrevidenciaria', 103, 13, TipoCampo::NUMERICO, descricao: 'Contribuicao previdenciaria (centavos)'),
            new CampoDTO('parcelaIsenta65Anos', 116, 13, TipoCampo::NUMERICO, descricao: 'Parcela isenta 65 anos (centavos)'),
            new CampoDTO('impostoRetidoFonte', 129, 13, TipoCampo::NUMERICO, descricao: 'Imposto retido na fonte (centavos)'),
            new CampoDTO('mesRecebimentoRRA', 142, 2, TipoCampo::NUMERICO, descricao: 'Mes do recebimento (01=Jan ... 12=Dez)'),
            new CampoDTO('metadadosRRA', 144, 5, TipoCampo::ALFANUMERICO),
            new CampoDTO('separador', 149, 1, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('flagCodigo150', 150, 3, TipoCampo::NUMERICO),
            new CampoDTO('numMesesRRA', 153, 1, TipoCampo::NUMERICO, descricao: 'Numero de meses RRA'),
            new CampoDTO('reservadoPos154', 154, 1, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('impostoBrutoRRA', 155, 13, TipoCampo::NUMERICO, descricao: 'Imposto bruto RRA — zero se abaixo da faixa tributavel'),
            new CampoDTO('rendimentosRRA', 168, 13, TipoCampo::NUMERICO, descricao: 'Rendimentos tributaveis RRA (centavos) — posicao correta'),
            new CampoDTO('rendimentosRRACopia', 181, 13, TipoCampo::NUMERICO, descricao: 'Copia dos rendimentos RRA (pos 168-180)'),
            new CampoDTO('campoDesconhecido194', 194, 13, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('checksum', 207, 10, TipoCampo::NUMERICO),
        );
    }
}
