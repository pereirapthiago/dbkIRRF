<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro IRPF (Header) - 1244 caracteres.
 * Primeiro registro do arquivo DBK.
 */
final readonly class RegistroHeaderDTO implements RegistroInterface
{
    public function __construct(
        // Identificacao
        public Cpf $cpf,
        public int $anoExercicio = 2026,
        public int $anoCalendario = 2025,
        public string $codigoVersao = '36',
        public TipoDeclaracao $tipoDeclaracao = TipoDeclaracao::ORIGINAL,

        // Contribuinte
        public string $codigoNaturezaOcupacao = '0000',
        public string $nome = '',
        public ?UnidadeFederativa $uf = null,
        public Data $dataNascimento = new Data('00000000'),
        public EstadoCivil $estadoCivil = EstadoCivil::SOLTEIRO,

        // Endereco
        public string $codigoMunicipioIbge = '0000',
        public string $codigoEnderecoMunicipio = '0000',
        public string $flagIdentificacaoContrib = '0',
        public string $cep = '00000000',
        public string $cidade = '',

        // Declaracao
        public string $reciboDeclaracaoAnterior = '',
        public ValorMonetario $impostoAPagar = new ValorMonetario(0),

        // Fonte pagadora principal
        public ?Cnpj $cnpjFontePrincipal = null,

        // Dependente/conjuge
        public ?Cpf $cpfDependenteConjuge = null,
        public ?Data $dataNascimentoDependente = null,

        // Medico/terceiro
        public ?Cpf $cpfMedicoTerceiro = null,

        // Sistema
        public string $sistemaOperacional = 'WINDOWS 11',
        public string $versaoSO = '10.0',
        public string $versaoProgramaIrpf = ' 17.0.16',

        // Conjuge
        public ?Cpf $cpfConjuge = null,

        // Campos internos (preservados para round-trip)
        public string $corRaca = '2',
        public string $hashValorCalculado = '00000000000',
        public string $tipoDeclaracaoNumero = '0',
        public string $flagSN = 'S',
        public string $flagPos254 = '0',
        public string $reciboNumeroControle = '0000000000000',
        public string $hashNumeroRecibo = '0000000000',
        public string $codigoControle = '',
        public ValorMonetario $valorPos701 = new ValorMonetario(0),
        public string $valoresFinanceirosRaw = '',
        public string $flagCpfMedicoRaw = '',
        public string $reservadoFinalRaw = '',
        public string $dataResidenciaPaisHeader = '00000000',
        public string $reservadoFinal2Raw = '',

        // Saida Definitiva (pos 19-20 e 673-692)
        public string $tipoModalidadeHeader = '00',
        public string $dataSaidaHeader = '00000000',
        public string $flagProcuradorHeader = '0',
        public ?Cpf $cpfProcuradorHeader = null,

        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::HEADER;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 4, TipoCampo::ALFA, descricao: 'Identificador IRPF'),
            new CampoDTO('reservado1', 5, 4, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('anoExercicio', 9, 4, TipoCampo::NUMERICO, descricao: 'Ano exercicio'),
            new CampoDTO('anoCalendario', 13, 4, TipoCampo::NUMERICO, descricao: 'Ano calendario (base)'),
            new CampoDTO('codigoVersao', 17, 2, TipoCampo::NUMERICO, descricao: 'Codigo/versao'),
            new CampoDTO('tipoModalidadeHeader', 19, 2, TipoCampo::NUMERICO, descricao: '00=Ajuste Anual, 20=Saida Definitiva'),
            new CampoDTO('tipoDeclaracao', 21, 1, TipoCampo::ALFA, descricao: '0=Original, 1=Retificadora, espaco=Saida'),
            new CampoDTO('cpf', 22, 11, TipoCampo::NUMERICO, descricao: 'CPF contribuinte'),
            new CampoDTO('reservado2', 33, 3, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('codigoNaturezaOcupacao', 36, 4, TipoCampo::NUMERICO, descricao: 'Cod natureza ocupacao'),
            new CampoDTO('nome', 40, 60, TipoCampo::ALFA, descricao: 'Nome completo'),
            new CampoDTO('uf', 100, 2, TipoCampo::ALFA, descricao: 'UF'),
            new CampoDTO('hashValorCalculado', 102, 11, TipoCampo::NUMERICO),
            new CampoDTO('dataNascimento', 113, 8, TipoCampo::DATA, descricao: 'Data nascimento (ddmmaaaa)'),
            new CampoDTO('estadoCivil', 121, 1, TipoCampo::ALFA, descricao: 'S/C/D/V/J'),
            new CampoDTO('tipoDeclaracaoNumero', 122, 1, TipoCampo::NUMERICO),
            new CampoDTO('flagSN', 123, 1, TipoCampo::ALFA),
            new CampoDTO('reciboRetificadora', 124, 10, TipoCampo::NUMERICO, descricao: 'Recibo decl anterior (se retificadora)'),
            new CampoDTO('reservado3', 134, 1, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('sistemaOperacional', 135, 14, TipoCampo::ALFA),
            new CampoDTO('versaoSO', 149, 6, TipoCampo::ALFA),
            new CampoDTO('versaoProgramaIrpf', 155, 12, TipoCampo::ALFA),
            new CampoDTO('reservado4', 167, 8, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('codigoMunicipioIbge', 175, 4, TipoCampo::NUMERICO),
            new CampoDTO('cpfConjuge', 179, 11, TipoCampo::NUMERICO, descricao: 'CPF do conjuge/companheiro'),
            new CampoDTO('reservado5b', 190, 1, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('reciboNumeroControle', 191, 13, TipoCampo::NUMERICO),
            new CampoDTO('reciboOriginal', 204, 10, TipoCampo::NUMERICO, descricao: 'Recibo decl anterior (se original)'),
            new CampoDTO('codigoEnderecoMunicipio', 214, 4, TipoCampo::NUMERICO),
            new CampoDTO('flagIdentificacaoContrib', 218, 1, TipoCampo::NUMERICO, descricao: 'Flag identificacao contribuinte'),
            new CampoDTO('cep', 219, 8, TipoCampo::NUMERICO, descricao: 'CEP'),
            new CampoDTO('reservadoZeros1', 227, 20, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('impostoAPagar', 247, 7, TipoCampo::NUMERICO, descricao: 'Imposto a pagar (centavos, 7 digitos)'),
            new CampoDTO('flagPos254', 254, 1, TipoCampo::NUMERICO),
            new CampoDTO('cpfRepetido', 255, 11, TipoCampo::NUMERICO),
            new CampoDTO('reservado6', 266, 40, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('reservadoZeros2', 306, 25, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('cnpjFontePrincipal', 331, 14, TipoCampo::NUMERICO, descricao: 'CNPJ fonte principal'),
            new CampoDTO('reservado7', 345, 40, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('cpfDependenteConjuge', 385, 11, TipoCampo::NUMERICO, descricao: 'CPF dependente/conjuge'),
            new CampoDTO('dataNascimentoDependente', 396, 8, TipoCampo::DATA),
            new CampoDTO('reservado8', 404, 97, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('cpfMedicoTerceiro', 501, 11, TipoCampo::NUMERICO),
            new CampoDTO('reservado9', 512, 39, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('cidade', 551, 40, TipoCampo::ALFA, descricao: 'Cidade'),
            new CampoDTO('nomeRepetido', 591, 60, TipoCampo::ALFA),
            new CampoDTO('hashNumeroRecibo', 651, 10, TipoCampo::NUMERICO),
            new CampoDTO('separadorHash', 661, 1, TipoCampo::ALFA, descricao: 'Separador fixo (espaco)'),
            new CampoDTO('codigoControle', 662, 12, TipoCampo::ALFANUMERICO),
            new CampoDTO('dataSaidaHeader', 674, 8, TipoCampo::NUMERICO, descricao: 'Data saida nao residente (aaaammdd)'),
            new CampoDTO('flagProcuradorHeader', 682, 1, TipoCampo::NUMERICO, descricao: '0=sem procurador, 1=com procurador'),
            new CampoDTO('cpfProcuradorHeader', 683, 11, TipoCampo::NUMERICO, descricao: 'CPF do procurador'),
            new CampoDTO('reservado10', 694, 7, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('valorPos701', 701, 13, TipoCampo::NUMERICO),
            new CampoDTO('reservado11', 714, 12, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('valoresFinanceirosRaw', 726, 53, TipoCampo::NUMERICO),
            new CampoDTO('reservado12a', 779, 14, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('corRaca', 793, 1, TipoCampo::NUMERICO, descricao: 'Cor/Raca (1=Indigena, 2=Branca...)'),
            new CampoDTO('reservado12b', 794, 67, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('flagCpfMedicoRaw', 861, 20, TipoCampo::ALFANUMERICO),
            new CampoDTO('reservadoFinalRaw', 881, 310, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('dataResidenciaPaisHeader', 1191, 8, TipoCampo::NUMERICO, descricao: 'Data residencia no pais destino (ddmmaaaa)'),
            new CampoDTO('reservadoFinal2Raw', 1199, 36, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('checksum', 1235, 10, TipoCampo::NUMERICO),
        );
    }
}
