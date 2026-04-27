<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 27 - Bens e Direitos - 1251 caracteres.
 */
final readonly class RegistroBemDireitoDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public string $codigoItem = '01',
        public string $flagExterior = '0',
        public string $pais = '105',
        public string $descricao = '',
        public ValorMonetario $valorAnterior = new ValorMonetario(0),
        public ValorMonetario $valorAtual = new ValorMonetario(0),

        // Endereco do bem (imoveis)
        public string $logradouro = '',
        public string $numero = '',
        public string $complemento = '',
        public string $bairro = '',
        public string $cep = '00000000',
        public string $uf = '',
        public string $codigoMunicipioIbge = '0000',
        public string $municipio = '',

        // Campos adicionais (posicoes 739-862, conteudo desconhecido)
        public string $camposAdicionaisRaw1 = '',

        // Dados bancarios — posicoes confirmadas na documentacao
        public string $agencia = '0000',
        public string $reservado867 = '',        // pos 867-879, 13 chars, funcao desconhecida
        public string $dvConta = ' ',

        // Campos adicionais (posicoes 881-896, conteudo desconhecido)
        public string $camposAdicionaisRaw2 = '',

        // Data de aquisicao — pos 897-904 (DDMMYYYY)
        public Data $dataAquisicao = new Data('00000000'),

        // Campos adicionais (posicoes 905-932, conteudo desconhecido)
        public string $reservado905 = '',

        // RENAVAM — pos 933-943 (condicional: grupo 02 / codigo 01 = veiculo)
        public string $renavam = '00000000000',

        public string $numeroConta = '0000000000000',

        // Campos adicionais (posicoes 957-1025, conteudo desconhecido)
        public string $camposAdicionaisRaw3 = '',

        // Investimento exterior (grupo 07 / cod 99)
        public ValorMonetario $aplicFinancRendPerda = new ValorMonetario(0),
        public ValorMonetario $aplicFinancImpExterior = new ValorMonetario(0),  // pos 1039-1051

        // CNPJ — pos 1042-1055 (demais grupos; vazio = espacos; parcial = zeros a esquerda)
        public string $cnpj = '',

        // Campos adicionais (posicoes 1056-1100, conteudo desconhecido)
        public string $camposAdicionaisRaw4 = '',

        public string $codigoGrupo = '01',          // pos 1101-1102: grupo do bem (01=Imoveis, 02=Moveis, etc)

        // Campos adicionais (posicoes 1104-1185, conteudo desconhecido)
        public string $camposAdicionaisRaw5a = '',

        // Aplic. financeiras — posicoes alternativas (variam por grupo/codigo)
        public ValorMonetario $aplicFinancRendPerdaAlt = new ValorMonetario(0),    // pos 1186-1198
        public ValorMonetario $aplicFinancImpExteriorAlt = new ValorMonetario(0),  // pos 1199-1211

        // Lucros e dividendos (posicoes 1212-1237, variam por grupo/codigo)
        public ValorMonetario $lucrosDivValorRecebido = new ValorMonetario(0),     // pos 1212-1224
        public ValorMonetario $lucrosDivImpostoPago = new ValorMonetario(0),       // pos 1225-1237

        // Campos adicionais (posicoes 1238-1241, conteudo desconhecido)
        public string $camposAdicionaisRaw5b = '',

        // Dados bancarios especificos — Grupo 06 (Depositos a vista e numerario)
        public string $agencia06 = '0000',            // pos 1023-1026
        public string $dvConta06 = ' ',               // pos 1040 (padding pos-agencia tem 13 chars: 1027-1039)
        public string $cnpjBanco06 = '',              // pos 1042-1055
        public string $codBacen06 = '000',            // pos 1086-1088
        public string $cpfTitular06 = '00000000000',  // pos 1090-1100
        public string $numeroConta06 = '',            // pos 1104-1112

        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::BEM_DIREITO;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('codigoItem', 14, 2, TipoCampo::NUMERICO, descricao: 'Codigo item (11=Apartamento, 12=Casa, etc)'),
            new CampoDTO('flagExterior', 16, 1, TipoCampo::NUMERICO, descricao: '0=nacional, 1=exterior'),
            new CampoDTO('pais', 17, 3, TipoCampo::NUMERICO, descricao: 'Pais (codigo 3 digitos, 105=Brasil, 149=Canada)'),
            new CampoDTO('descricao', 20, 500, TipoCampo::ALFA, descricao: 'Descricao/discriminacao'),
            new CampoDTO('reservado1', 520, 12, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('valorAnterior', 532, 13, TipoCampo::NUMERICO, descricao: 'Valor em 31/12 anterior (centavos)'),
            new CampoDTO('valorAtual', 545, 13, TipoCampo::NUMERICO, descricao: 'Valor em 31/12 atual (centavos)'),
            new CampoDTO('logradouro', 558, 40, TipoCampo::ALFA),
            new CampoDTO('numero', 598, 6, TipoCampo::ALFANUMERICO),
            new CampoDTO('complemento', 604, 40, TipoCampo::ALFA),
            new CampoDTO('bairro', 644, 40, TipoCampo::ALFA),
            new CampoDTO('cep', 684, 8, TipoCampo::NUMERICO),
            new CampoDTO('separador1', 692, 1, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('uf', 693, 2, TipoCampo::ALFA),
            new CampoDTO('codigoMunicipioIbge', 695, 4, TipoCampo::NUMERICO),
            new CampoDTO('municipio', 699, 40, TipoCampo::ALFA),
            new CampoDTO('camposAdicionaisRaw1', 739, 124, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 739-862 — conteudo desconhecido'),
            new CampoDTO('agencia', 863, 4, TipoCampo::NUMERICO, descricao: 'Agencia bancaria'),
            new CampoDTO('reservado867', 867, 13, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 867-879 — funcao desconhecida'),
            new CampoDTO('dvConta', 880, 1, TipoCampo::ALFANUMERICO, descricao: 'Digito verificador da conta'),
            new CampoDTO('camposAdicionaisRaw2', 881, 16, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 881-896 — conteudo desconhecido'),
            new CampoDTO('dataAquisicao', 897, 8, TipoCampo::NUMERICO, descricao: 'Data de aquisicao DDMMYYYY'),
            new CampoDTO('reservado905', 905, 28, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 905-932 — conteudo desconhecido'),
            new CampoDTO('renavam', 933, 11, TipoCampo::NUMERICO, descricao: 'RENAVAM (condicional: grupo 02 / codigo 01)'),
            new CampoDTO('numeroConta', 944, 13, TipoCampo::NUMERICO, descricao: 'Numero da conta bancaria'),
            new CampoDTO('camposAdicionaisRaw3', 957, 69, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 957-1025 — conteudo desconhecido'),
            new CampoDTO('aplicFinancRendPerda', 1026, 13, TipoCampo::NUMERICO, descricao: 'Aplic Financ: Renda ou Perda (centavos)'),
            new CampoDTO('aplicFinancImpExterior', 1039, 13, TipoCampo::NUMERICO, descricao: 'Aplic Financ: Imposto pago Exterior — grupo 07 / cod 99 (centavos)'),
            new CampoDTO('cnpj', 1042, 14, TipoCampo::ALFA, descricao: 'CNPJ — demais grupos (sobrepos. parcial c/ aplicFinancImpExterior)'),
            new CampoDTO('camposAdicionaisRaw4', 1056, 45, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 1056-1100 — conteudo desconhecido'),
            new CampoDTO('codigoGrupo', 1101, 2, TipoCampo::NUMERICO, descricao: 'Codigo grupo (01=Imoveis, 02=Moveis, etc)'),
            new CampoDTO('reservado1103', 1103, 1, TipoCampo::NUMERICO, obrigatorio: false, descricao: 'Posicao 1103 — constante "0"'),
            new CampoDTO('camposAdicionaisRaw5a', 1104, 82, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 1104-1185 — conteudo desconhecido'),
            new CampoDTO('aplicFinancRendPerdaAlt', 1186, 13, TipoCampo::NUMERICO, descricao: 'Aplic Fin. Renda ou Perda — posicao alt (centavos)'),
            new CampoDTO('aplicFinancImpExteriorAlt', 1199, 13, TipoCampo::NUMERICO, descricao: 'Aplic Fin. Imposto pago Exterior — posicao alt (centavos)'),
            new CampoDTO('lucrosDivValorRecebido', 1212, 13, TipoCampo::NUMERICO, descricao: 'Lucros e Div.: Valor recebido (centavos)'),
            new CampoDTO('lucrosDivImpostoPago', 1225, 13, TipoCampo::NUMERICO, descricao: 'Lucros e Div.: Imposto pago Exterior/IRRF Brasil (centavos)'),
            new CampoDTO('camposAdicionaisRaw5b', 1238, 4, TipoCampo::ALFA, obrigatorio: false, descricao: 'Posicoes 1238-1241 — conteudo desconhecido'),

            // Campos condicionais — Grupo 06 (Depositos a vista e numerario)
            // Sobrepoem posicoes de outros campos; so sao gravados/lidos quando codigoGrupo = '06'
            // Padding pos-agencia tem 13 chars (1027-1039), deslocando DV e subsequentes em +1
            new CampoDTO('agencia06', 1023, 4, TipoCampo::NUMERICO, obrigatorio: false, descricao: '[grupo 06] Agencia bancaria (sobrepoe final de camposAdicionaisRaw3)'),
            new CampoDTO('dvConta06', 1040, 1, TipoCampo::ALFANUMERICO, obrigatorio: false, descricao: '[grupo 06] DV conta (pos 1040)'),
            new CampoDTO('separador06', 1041, 1, TipoCampo::ALFA, obrigatorio: false, descricao: '[grupo 06] Separador espaco entre DV e CNPJ (pos 1041)'),
            new CampoDTO('cnpjBanco06', 1042, 14, TipoCampo::NUMERICO, obrigatorio: false, descricao: '[grupo 06] CNPJ instituicao financeira (pos 1042-1055)'),
            new CampoDTO('codBacen06', 1086, 3, TipoCampo::NUMERICO, obrigatorio: false, descricao: '[grupo 06] Codigo BACEN banco (pos 1086-1088)'),
            new CampoDTO('separadorT06', 1089, 1, TipoCampo::ALFA, obrigatorio: false, descricao: '[grupo 06] Separador "T" (pos 1089)'),
            new CampoDTO('cpfTitular06', 1090, 11, TipoCampo::NUMERICO, obrigatorio: false, descricao: '[grupo 06] CPF titular referencia (pos 1090-1100)'),
            new CampoDTO('numeroConta06', 1104, 9, TipoCampo::ALFANUMERICO, obrigatorio: false, descricao: '[grupo 06] Numero da conta bancaria (dentro de camposAdicionaisRaw5a)'),

            new CampoDTO('checksum', 1242, 10, TipoCampo::NUMERICO),
        );
    }
}
