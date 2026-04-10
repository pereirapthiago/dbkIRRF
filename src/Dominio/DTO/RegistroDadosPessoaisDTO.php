<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\FlagSimNao;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;

/**
 * Registro 16 - Dados Pessoais do Contribuinte - 930 caracteres.
 */
final readonly class RegistroDadosPessoaisDTO implements RegistroInterface
{
    public function __construct(
        // Identificacao
        public Cpf $cpf,
        public string $nome = '',

        // Endereco
        public string $tipoLogradouro = '',
        public string $logradouro = '',
        public string $numero = '',
        public string $complemento = '',
        public string $bairro = '',
        public string $cep = '00000000',
        public string $codigoMunicipioIbge = '0000',
        public string $municipio = '',
        public ?UnidadeFederativa $uf = null,

        // Contato
        public string $email = '',
        public string $dddCelular = '',
        public string $celular = '',
        public string $dddFixo = '',
        public string $telefoneFixo = '',

        // Pessoais
        public Data $dataNascimento = new Data('00000000'),
        public string $codigoOcupacao = '000',
        public ?Cpf $cpfConjuge = null,

        // Declaracao
        public TipoDeclaracao $tipoDeclaracao = TipoDeclaracao::ORIGINAL,
        public FlagSimNao $flagAlteracaoCadastral = FlagSimNao::NAO,
        public string $reciboDeclaracaoAnterior = '',

        // Residencia no pais
        public string $flagResidenciaPais = ' ',
        public Data $dataResidenciaPais = new Data('        '),

        // Campos internos (preservados para round-trip)
        public string $reservadoCodigo = '   105',
        public string $flagPos381 = ' ',
        public string $naturezaOcupacao = '00',
        public string $flagSN387 = '0',
        public string $flagNS388 = 'S',
        public string $flagNS390 = 'S',
        public string $flagA454 = 'A',
        public FlagSimNao $flagPossuiConjuge = FlagSimNao::NAO,
        public string $camposAdicionaisRaw = '',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::DADOS_PESSOAIS;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('nome', 14, 60, TipoCampo::ALFA),
            new CampoDTO('tipoLogradouro', 74, 15, TipoCampo::ALFA),
            new CampoDTO('logradouro', 89, 40, TipoCampo::ALFA),
            new CampoDTO('numero', 129, 6, TipoCampo::ALFANUMERICO),
            new CampoDTO('complemento', 135, 20, TipoCampo::ALFA),
            new CampoDTO('separadorComplementoBairro', 155, 1, TipoCampo::ALFA, obrigatorio: false, descricao: 'Separador obrigatorio (sempre espaco)'),
            new CampoDTO('bairro', 156, 19, TipoCampo::ALFA),
            new CampoDTO('cep', 175, 8, TipoCampo::NUMERICO),
            new CampoDTO('separador', 183, 1, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('codigoMunicipioIbge', 184, 4, TipoCampo::NUMERICO),
            new CampoDTO('municipio', 188, 40, TipoCampo::ALFA),
            new CampoDTO('uf', 228, 2, TipoCampo::ALFA),
            new CampoDTO('reservadoCodigo', 230, 6, TipoCampo::ALFANUMERICO),
            new CampoDTO('email', 236, 60, TipoCampo::ALFA),
            new CampoDTO('reservado1', 296, 41, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('cpfConjuge', 337, 11, TipoCampo::NUMERICO),
            new CampoDTO('dddFixo', 348, 2, TipoCampo::NUMERICO),
            new CampoDTO('reservado2', 350, 11, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('dataNascimento', 361, 8, TipoCampo::DATA),
            new CampoDTO('reservado3', 369, 12, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('flagPos381', 381, 1, TipoCampo::NUMERICO),
            new CampoDTO('codigoOcupacao', 382, 3, TipoCampo::NUMERICO, descricao: 'Campo de funcao desconhecida (BAIXA confianca)'),
            new CampoDTO('naturezaOcupacao', 385, 2, TipoCampo::NUMERICO, descricao: 'Campo de funcao desconhecida (BAIXA confianca)'),
            new CampoDTO('flagSN387', 387, 1, TipoCampo::ALFA, descricao: 'Constante "1" em todos os arquivos analisados — funcao desconhecida'),
            new CampoDTO('flagNS388', 388, 1, TipoCampo::ALFA, descricao: 'Hipotese: S=endereco no Brasil / N=exterior (BAIXA confianca) — sempre "S" nos arquivos testados'),
            new CampoDTO('flagRetificadora', 389, 1, TipoCampo::ALFA, descricao: 'Hipotese interna: N=Original / S=Retificadora — doc sugere: doenca grave (BAIXA confianca, nao confirmado contra DBK real)'),
            new CampoDTO('flagNS390', 390, 1, TipoCampo::ALFA, descricao: 'Constante "S" em todos os arquivos analisados — funcao desconhecida'),
            new CampoDTO('flagAlteracaoCadastral', 391, 1, TipoCampo::ALFA, descricao: 'N=sem alteracoes cadastrais / S=houve alteracoes (endereco, conjuge, residencia)'),
            new CampoDTO('reciboRetificadora', 392, 10, TipoCampo::NUMERICO, descricao: 'Recibo (se retificadora)'),
            new CampoDTO('reservado4', 402, 42, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('reciboOriginal', 444, 10, TipoCampo::NUMERICO, descricao: 'Recibo (se original)'),
            new CampoDTO('flagA454', 454, 1, TipoCampo::ALFA),
            new CampoDTO('reservado5', 455, 31, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('dddCelular', 486, 2, TipoCampo::NUMERICO),
            new CampoDTO('celular', 488, 9, TipoCampo::NUMERICO),
            new CampoDTO('flagPossuiConjuge', 497, 1, TipoCampo::ALFA, descricao: 'N=sem conjuge/companheiro(a) / S=possui conjuge/companheiro(a)'),
            new CampoDTO('telefoneFixo', 498, 8, TipoCampo::NUMERICO),
            new CampoDTO('camposAdicionaisRaw', 506, 366, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('flagResidenciaPais', 872, 1, TipoCampo::NUMERICO, descricao: '0=nao era residente no exterior / 1=era residente no exterior e retornou ao Brasil no ano'),
            new CampoDTO('dataResidenciaPais', 873, 8, TipoCampo::DATA),
            new CampoDTO('reservado6', 881, 40, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('checksum', 921, 10, TipoCampo::NUMERICO),
        );
    }
}
