<?php

/**
 * Exemplo de uso da biblioteca DbkIrrf.
 *
 * Lê elaboracao.json (estrutura do sistema Atlantic), mapeia para
 * DeclaracaoDTO e gera o arquivo .DBK pronto para importar no IRPF 2026.
 *
 * Uso:
 *   php exemplo/gerar.php
 *   php exemplo/gerar.php exemplo/elaboracao.json
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DbkIrrf\Aplicacao\Servico\GeradorDbk;
use DbkIrrf\Aplicacao\Servico\NomeadorArquivo;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\DTO\RegistroExigibilidadeSuspensaDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento84DTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\DTO\RegistroSaidaDefinitivaDTO;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\FlagSimNao;
use DbkIrrf\Dominio\Enum\ModalidadeDeclaracao;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

// =============================================================================
// Helpers — equivalentes locais dos ConversorData e ConversorMonetario da API.
// Adaptados para aceitar datas como strings ISO (YYYY-MM-DD) vindas do JSON.
// =============================================================================

/**
 * Converte string "YYYY-MM-DD" para Data (ddmmaaaa).
 * Retorna Data::vazia() quando a entrada é nula ou inválida.
 */
function converterData(?string $valor): Data
{
    if ($valor === null || $valor === '') {
        return Data::vazia();
    }
    $dt = DateTime::createFromFormat('Y-m-d', $valor);
    return $dt ? new Data($dt->format('dmY')) : Data::vazia();
}

/**
 * Converte string "YYYY-MM-DD" para Data de espaços (para campos opcionais
 * que usam espaços quando ausentes, como data de retorno do exterior).
 */
function converterDataOuEspacos(?string $valor): Data
{
    if ($valor === null || $valor === '') {
        return Data::espacosVazios();
    }
    $dt = DateTime::createFromFormat('Y-m-d', $valor);
    return $dt ? new Data($dt->format('dmY')) : Data::espacosVazios();
}

/**
 * Converte string "YYYY-MM-DD" para o formato AAAAMMDD (usado no header de saída).
 */
function converterDataAaaamm(?string $valor): string
{
    if ($valor === null || $valor === '') {
        return '00000000';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $valor);
    return $dt ? $dt->format('Ymd') : '00000000';
}

/**
 * Converte string de reais (ex: "1500.00") para ValorMonetario (centavos).
 * Nunca retorna valor negativo.
 */
function converterMonetario(string $valor): ValorMonetario
{
    $centavos = (int) round(((float) $valor) * 100);
    return new ValorMonetario(max(0, $centavos));
}

/**
 * Resolve CPF a partir de string raw (remove não-dígitos).
 * Retorna null se o CPF não tiver 11 dígitos.
 */
function resolverCpf(?string $raw): ?Cpf
{
    if (empty($raw)) {
        return null;
    }
    $limpo = preg_replace('/\D/', '', $raw);
    return strlen($limpo) === 11 ? new Cpf($limpo) : null;
}

/**
 * Resolve CNPJ a partir de string raw (remove não-dígitos).
 * Retorna null se o CNPJ não tiver 14 dígitos.
 */
function resolverCnpj(?string $raw): ?Cnpj
{
    if (empty($raw)) {
        return null;
    }
    $limpo = preg_replace('/\D/', '', $raw);
    return strlen($limpo) === 14 ? new Cnpj($limpo) : null;
}

// =============================================================================
// Carrega e decodifica o JSON
// =============================================================================

$caminhoJson = $argv[1] ?? __DIR__ . '/elaboracao.json';

if (!file_exists($caminhoJson)) {
    fwrite(STDERR, "Arquivo não encontrado: {$caminhoJson}\n");
    exit(1);
}

$elab = json_decode(file_get_contents($caminhoJson));
if ($elab === null) {
    fwrite(STDERR, "JSON inválido: " . json_last_error_msg() . "\n");
    exit(1);
}

// =============================================================================
// Dados base
// =============================================================================

$cpf           = new Cpf($elab->pessoa->cpf);
$anoCalendario = (int) $elab->ano;
$ehRetificadora = !empty($elab->numero_recibo_anterior);
$ehSaida        = ((int) ($elab->tipo_id ?? 1)) === 2;
$info           = $elab->elab_info_pessoai ?? new stdClass();
$endereco       = $elab->elab_endereco     ?? new stdClass();
$saida          = $elab->elab_saida        ?? null;

$declaracao = new DeclaracaoDTO();
$declaracao->modalidade = $ehSaida ? ModalidadeDeclaracao::SAIDA : ModalidadeDeclaracao::ANUAL;

// =============================================================================
// Seção: Header (registro IRPF)
// Equivalente: MapeadorHeader
// =============================================================================

$cnpjPrincipal = resolverCnpj($elab->elab_rend_pj[0]->cpf_cnpj ?? null);

$nomePessoa = !empty($elab->pessoa->nome_completo)
    ? mb_strtoupper($elab->pessoa->nome_completo)
    : mb_strtoupper(trim(($elab->pessoa->primeiro_nome ?? '') . ' ' . ($elab->pessoa->ultimo_nome ?? '')));

$declaracao->header = new RegistroHeaderDTO(
    cpf:                    $cpf,
    anoExercicio:           $anoCalendario + 1,
    anoCalendario:          $anoCalendario,
    codigoVersao:           '36',
    tipoDeclaracao:         $ehRetificadora ? TipoDeclaracao::RETIFICADORA : TipoDeclaracao::ORIGINAL,
    tipoModalidadeHeader:   $ehSaida ? '20' : '00',
    dataSaidaHeader:        $ehSaida && $saida !== null
                                ? converterDataAaaamm($saida->dt_nao_residente ?? null)
                                : '00000000',
    flagProcuradorHeader:   $ehSaida && !empty($saida?->cpf) ? '1' : '0',
    cpfProcuradorHeader:    $ehSaida && !empty($saida?->cpf)
                                ? resolverCpf($saida->cpf)
                                : null,
    dataResidenciaPaisHeader: $ehSaida && $saida !== null
                                ? converterData($saida->dt_residente ?? null)->valor
                                : '00000000',
    codigoNaturezaOcupacao: isset($info->elab_naturezas_ocupacao->codigo)
                                ? str_pad($info->elab_naturezas_ocupacao->codigo, 4, '0', STR_PAD_LEFT)
                                : '0000',
    nome:                   $nomePessoa,
    uf:                     !empty($endereco->uf) ? UnidadeFederativa::tryFrom($endereco->uf) : null,
    dataNascimento:         converterData($elab->pessoa->nascimento ?? null),
    estadoCivil:            !empty($info->possui_conjuge) ? EstadoCivil::CASADO : EstadoCivil::SOLTEIRO,
    cep:                    !empty($endereco->cep) ? preg_replace('/\D/', '', $endereco->cep) : '00000000',
    cidade:                 mb_strtoupper($endereco->cidade ?? ''),
    reciboDeclaracaoAnterior: $elab->numero_recibo_anterior ?? '',
    cnpjFontePrincipal:     $cnpjPrincipal,
    cpfConjuge:             resolverCpf($info->cpf_conjuge ?? null),
    cpfDependenteConjuge:   resolverCpf($info->cpf_conjuge ?? null),
);

// =============================================================================
// Seção: Dados Pessoais (registro 16)
// Equivalente: MapeadorDadosPessoais
// =============================================================================

$declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
    cpf:                    $cpf,
    nome:                   $nomePessoa,
    logradouro:             mb_strtoupper($endereco->endereco ?? ''),
    numero:                 $endereco->numero ?? '',
    complemento:            mb_strtoupper($endereco->complemento ?? ''),
    bairro:                 mb_strtoupper($endereco->bairro ?? ''),
    cep:                    !empty($endereco->cep) ? preg_replace('/\D/', '', $endereco->cep) : '00000000',
    municipio:              mb_strtoupper($endereco->cidade ?? ''),
    uf:                     !empty($endereco->uf) ? UnidadeFederativa::tryFrom($endereco->uf) : null,
    email:                  $info->email ?? '',
    dddCelular:             str_pad((string) ((int) ($info->ddd ?? 0)), 2, '0', STR_PAD_LEFT),
    celular:                $info->telefone ?? '',
    dataNascimento:         converterData($info->dt_nascimento ?? $elab->pessoa->nascimento ?? null),
    codigoOcupacao:         isset($info->elab_ocupacoes_principai->codigo)
                                ? str_pad($info->elab_ocupacoes_principai->codigo, 3, '0', STR_PAD_LEFT)
                                : '000',
    naturezaOcupacao:       isset($info->elab_naturezas_ocupacao->codigo)
                                ? str_pad($info->elab_naturezas_ocupacao->codigo, 2, '0', STR_PAD_LEFT)
                                : '00',
    cpfConjuge:             resolverCpf($info->cpf_conjuge ?? null),
    tipoDeclaracao:         $ehRetificadora ? TipoDeclaracao::RETIFICADORA : TipoDeclaracao::ORIGINAL,
    flagA454:               $ehSaida ? 'S' : 'A',
    flagAlteracaoCadastral: !empty($info->houve_alteracao) ? FlagSimNao::SIM : FlagSimNao::NAO,
    reciboDeclaracaoAnterior: $elab->numero_recibo_anterior ?? '',
    flagPossuiConjuge:      !empty($info->possui_conjuge) ? FlagSimNao::SIM : FlagSimNao::NAO,
    flagResidenciaPais:     !empty($info->residente_exterior) ? '1' : ' ',
    dataResidenciaPais:     converterDataOuEspacos($info->dt_retorno ?? null),
);

// =============================================================================
// Seção: Rendimentos de Pessoa Jurídica (registro 21)
// Equivalente: MapeadorRendimentosPJ
// =============================================================================

foreach ($elab->elab_rend_pj ?? [] as $rendPJ) {
    $declaracao->adicionarRendimentoPJ(new RegistroRendimentosPJDTO(
        cpf:                    $cpf,
        cnpjFontePagadora:      new Cnpj($rendPJ->cpf_cnpj),
        nomeFontePagadora:      mb_strtoupper($rendPJ->nome ?? ''),
        rendimentosRecebidos:   converterMonetario($rendPJ->rendimento ?? '0'),
        contribPrevidenciaria:  converterMonetario($rendPJ->contribuicao ?? '0'),
        decimoTerceiroSalario:  converterMonetario($rendPJ->decimo_terceiro ?? '0'),
        impostoRetidoFonte:     converterMonetario($rendPJ->imposto ?? '0'),
        irrfDecimoTerceiro:     converterMonetario($rendPJ->imposto_decimo_terceiro ?? '0'),
    ));
}

// =============================================================================
// Seção: Rendimentos Mensais — PF/Exterior (registro 22)
// Equivalente: MapeadorRendimentosMensais
// =============================================================================

foreach ($elab->elab_rend_pf_exterior ?? [] as $mes) {
    $rendNaoAssal = converterMonetario($mes->trabalho_nao_assalariado ?? '0');
    $temporada    = converterMonetario($mes->alugueis ?? '0');
    $outros       = converterMonetario($mes->outros ?? '0');
    $exterior     = converterMonetario($mes->exterior ?? '0');

    $totalMes = new ValorMonetario(
        $rendNaoAssal->centavos + $temporada->centavos + $outros->centavos + $exterior->centavos
    );

    $declaracao->adicionarRendimentoMensal(new RegistroRendimentosMensaisDTO(
        cpf:                    $cpf,
        mesReferencia:          (int) $mes->mes,
        rendNaoAssalariado:     $rendNaoAssal,
        temporada:              $temporada,
        outrosRendimentos:      $outros,
        exterior:               $exterior,
        previdencia:            converterMonetario($mes->previdencia_oficial ?? '0'),
        dependentes:            new ValorMonetario(((int) ($mes->qtd_dependentes ?? 0)) * 18959),
        pensaoAlimenticia:      converterMonetario($mes->pensao_alimenticia ?? '0'),
        livroCaixa:             converterMonetario($mes->livro_caixa ?? '0'),
        totalRendimentosMes:    $totalMes,
        darfPago:               converterMonetario($mes->darf_pago ?? '0'),
    ));
}

// =============================================================================
// Seção: Imposto Pago (registro 23)
// Equivalente: MapeadorImpostosPagos
// =============================================================================

$camposImpostoPago = [
    '0001' => 'complementar',
    '0002' => 'exterior',
    '0004' => 'retido_titular',
    '0005' => 'retido_dependente',
    '0006' => 'carne_titular',
    '0007' => 'carne_dependente',
];

$impostoPago = $elab->elab_imposto_pago ?? null;
if ($impostoPago !== null) {
    foreach ($camposImpostoPago as $codigo => $campo) {
        $valor = converterMonetario($impostoPago->{$campo} ?? '0');
        if ($valor->centavos === 0) {
            continue;
        }
        $declaracao->adicionarImpostoPago(new RegistroImpostoPagoDTO(
            cpf:    $cpf,
            codigo: $codigo,
            valor:  $valor,
        ));
    }
}

// =============================================================================
// Seção: Deduções Legais — Previdência Oficial (registro 24)
// Equivalente: MapeadorDeducoesLegais
// =============================================================================

$totalPrevidencia = 0;
foreach ($elab->elab_rend_pj ?? [] as $rendPJ) {
    $totalPrevidencia += converterMonetario($rendPJ->contribuicao ?? '0')->centavos;
}

if ($totalPrevidencia > 0) {
    $declaracao->adicionarDeducaoLegal(new RegistroDeducaoLegalDTO(
        cpf:            $cpf,
        codigoDeducao:  '0001',
        valor:          new ValorMonetario($totalPrevidencia),
    ));
}

// =============================================================================
// Seção: Dependentes (registro 25)
// Equivalente: MapeadorDependentes
// =============================================================================

$mapaParentesco = [
    1 => '11',  // Companheiro(a)
    2 => '21',  // Filho(a) ou enteado(a) até 21 anos
    3 => '22',  // Filho(a) ou enteado(a) incapaz
    4 => '24',  // Filho(a) ou enteado(a) universitário até 24 anos
    5 => '31',  // Irmão(a), neto(a) ou bisneto(a) até 21 anos
    6 => '41',  // Pais, avós e bisavós
    7 => '51',  // Menor pobre até 21 anos
    8 => '61',  // Pessoa absolutamente incapaz
];

$seqDependente = 1;
foreach ($elab->elab_dependentes ?? [] as $dep) {
    $parentescoId = (int) ($dep->parentesco_id ?? 0);
    $declaracao->adicionarDependente(new RegistroDependenteDTO(
        cpf:            $cpf,
        sequencial:     $seqDependente,
        tipoDependente: new CodigoDependente($mapaParentesco[$parentescoId] ?? '99'),
        nomeDependente: mb_strtoupper($dep->nome ?? ''),
        dataNascimento: converterData($dep->dt_nascimento ?? null),
        cpfDependente:  resolverCpf($dep->cpf ?? null),
        moraTitular:    (bool) ($dep->mora_titular ?? false),
        email:          mb_strtoupper($dep->email ?? ''),
        ddd:            str_pad((string) ((int) ($dep->ddd ?? 0)), 2, '0', STR_PAD_LEFT),
        celular:        $dep->telefone ?? '',
    ));
    $seqDependente++;
}

// =============================================================================
// Seção: Pagamentos e Doações a Terceiros (registro 26)
// Equivalente: MapeadorPagamentos
// =============================================================================

foreach ($elab->elab_pagamentos ?? [] as $pag) {
    $declaracao->adicionarPagamento(new RegistroPagamentoDTO(
        cpf:                $cpf,
        codigoPagamento:    isset($pag->elab_tipo_pagamento->codigo)
                                ? str_pad($pag->elab_tipo_pagamento->codigo, 2, '0', STR_PAD_LEFT)
                                : '00',
        cpfCnpjBeneficiario: preg_replace('/\D/', '', $pag->cpf_cnpj ?? ''),
        nomeBeneficiario:   mb_strtoupper($pag->nome ?? ''),
        valorPago:          converterMonetario($pag->valor ?? '0'),
        parcelaNaoDedutivel: converterMonetario($pag->parcela ?? '0'),
        descricao:          mb_strtoupper($pag->descricao ?? ''),
    ));
}

// =============================================================================
// Seção: Bens e Direitos (registro 27)
// Equivalente: MapeadorBensDireitos
// =============================================================================

$indiceBem = 0;
foreach ($elab->elab_bens ?? [] as $bem) {
    $indiceBem++;
    $tipoBem      = $bem->elab_tipo_ben      ?? new stdClass();
    $enderecoBem  = $bem->elab_bens_endereco ?? new stdClass();
    $codigoItem   = str_pad((string) ($tipoBem->codigo       ?? '99'), 2, '0', STR_PAD_LEFT);
    $codigoGrupo  = str_pad((string) ($tipoBem->grupo_codigo ?? '01'), 2, '0', STR_PAD_LEFT);

    // Código de país: 105 = Brasil; bens no exterior possuem pais->descricao com o código numérico
    $paisCodigo = '105';
    if (isset($bem->pais->descricao)) {
        $paisCodigo = str_pad(preg_replace('/\D/', '', $bem->pais->descricao), 3, '0', STR_PAD_LEFT);
    }

    $renavamRaw = preg_replace('/\D/', '', $bem->renavam ?? '');
    $renavam    = $renavamRaw !== '' ? str_pad($renavamRaw, 11, '0', STR_PAD_LEFT) : '00000000000';

    $declaracao->adicionarBemDireito(new RegistroBemDireitoDTO(
        cpf:                      $cpf,
        codigoItem:               $codigoItem,
        flagExterior:             $paisCodigo !== '105' ? '1' : '0',
        pais:                     $paisCodigo,
        descricao:                mb_strtoupper($bem->descricao ?? ''),
        valorAnterior:            converterMonetario($bem->valor_inicio ?? '0'),
        valorAtual:               converterMonetario($bem->valor_fim   ?? '0'),
        logradouro:               mb_strtoupper($enderecoBem->logradouro  ?? ''),
        numero:                   $enderecoBem->numero                   ?? '',
        complemento:              mb_strtoupper($enderecoBem->complemento ?? ''),
        bairro:                   mb_strtoupper($enderecoBem->bairro      ?? ''),
        cep:                      preg_replace('/\D/', '', $enderecoBem->cep ?? '00000000'),
        uf:                       mb_strtoupper($enderecoBem->uf           ?? ''),
        municipio:                mb_strtoupper($enderecoBem->cidade       ?? ''),
        dataAquisicao:            converterData($bem->data_compra          ?? null),
        renavam:                  $renavam,
        aplicFinancRendPerda:     converterMonetario($bem->aplic_financ_rend_perda   ?? '0'),
        aplicFinancImpExterior:   converterMonetario($bem->aplic_financ_imp_exterior ?? '0'),
        codigoGrupo:              $codigoGrupo,
        aplicFinancRendPerdaAlt:  converterMonetario($bem->aplic_financ_rend_perda   ?? '0'),
        aplicFinancImpExteriorAlt: converterMonetario($bem->aplic_financ_imp_exterior ?? '0'),
        lucrosDivValorRecebido:   converterMonetario($bem->lucros_div_valor_recebido ?? '0'),
        lucrosDivImpostoPago:     converterMonetario($bem->lucros_div_imposto_pago   ?? '0'),
    ));
}

// =============================================================================
// Seção: Dívidas e Ônus Reais (registro 28)
// Equivalente: MapeadorDividas
// =============================================================================

foreach ($elab->elab_dividas ?? [] as $divida) {
    $declaracao->adicionarDivida(new RegistroDividaDTO(
        cpf:            $cpf,
        codigoDivida:   isset($divida->elab_tipo_divida->codigo)
                            ? str_pad((string) $divida->elab_tipo_divida->codigo, 2, '0', STR_PAD_LEFT)
                            : '11',
        descricao:      mb_strtoupper($divida->descricao ?? ''),
        saldoAnterior:  converterMonetario($divida->valor_inicio ?? '0'),
        saldoAtual:     converterMonetario($divida->valor_fim ?? '0'),
        valorPagoAno:   converterMonetario($divida->valor_pago ?? '0'),
    ));
}

// =============================================================================
// Seção: Rendimentos Recebidos Acumuladamente — RRA (registro 45)
// Equivalente: MapeadorRras
// =============================================================================

foreach ($elab->elab_rend_acumulados ?? [] as $rra) {
    $rendimentos = converterMonetario($rra->rendimento ?? '0');
    $declaracao->adicionarRra(new RegistroRraDTO(
        cpf:                    $cpf,
        cnpjFontePagadora:      new Cnpj($rra->cpf_cnpj),
        nomeFontePagadora:      mb_strtoupper($rra->nome ?? ''),
        contribPrevidenciaria:  converterMonetario($rra->contribuicao ?? '0'),
        impostoRetidoFonte:     converterMonetario($rra->imposto ?? '0'),
        mesRecebimentoRRA:      str_pad((string) ($rra->mes ?? 1), 2, '0', STR_PAD_LEFT),
        numMesesRRA:            (string) ($rra->numero_meses ?? 0),
        rendimentosRRA:         $rendimentos,
        rendimentosRRACopia:    $rendimentos,
    ));
}

// =============================================================================
// Seção: Exigibilidade Suspensa (registro 80)
// Equivalente: MapeadorExigibilidadesSuspensas
// =============================================================================

foreach ($elab->elab_rend_exigibilidades ?? [] as $exig) {
    $declaracao->adicionarExigibilidadeSuspensa(new RegistroExigibilidadeSuspensaDTO(
        cpf:                        $cpf,
        cnpjFontePagadora:          new Cnpj($exig->cpf_cnpj),
        nomeFontePagadora:          mb_strtoupper($exig->nome ?? ''),
        rendimentosTributaveis:     converterMonetario($exig->rendimentos ?? '0'),
        depositosJudiciais:         converterMonetario($exig->depositos ?? '0'),
    ));
}

// =============================================================================
// Seção: Rendimentos Isentos e Não Tributáveis (registro 84)
// Equivalente: MapeadorRendimentosIsentos
// =============================================================================

foreach ($elab->elab_rend_isentos ?? [] as $isento) {
    $declaracao->adicionarRendimentoIsento84(new RegistroRendimentoIsento84DTO(
        cpf:                    $cpf,
        tipoBeneficiario:       ((int) ($isento->tipo_beneficiario_id ?? 1)) === 1
                                    ? TipoBeneficiario::TITULAR
                                    : TipoBeneficiario::DEPENDENTE,
        cpfBeneficiario:        $cpf,
        codigoTipoRendimento:   isset($isento->elab_tipo_rend_isento->codigo)
                                    ? str_pad($isento->elab_tipo_rend_isento->codigo, 4, '0', STR_PAD_LEFT)
                                    : '0001',
        cnpjFontePagadora:      new Cnpj($isento->cpf_cnpj),
        nomeFontePagadora:      mb_strtoupper($isento->nome ?? ''),
        valorRendimentoIsento:  converterMonetario($isento->valor ?? '0'),
    ));
}

// =============================================================================
// Seção: Tributação Exclusiva/Definitiva (registro 88)
// Equivalente: MapeadorTribExclusivas
// =============================================================================

foreach ($elab->elab_rend_exclusivas ?? [] as $exclusiva) {
    $declaracao->adicionarTribExclusiva(new RegistroTribExclusivaDTO(
        cpf:                    $cpf,
        tipoBeneficiario:       ((int) ($exclusiva->tipo_beneficiario_id ?? 1)) === 1
                                    ? TipoBeneficiario::TITULAR
                                    : TipoBeneficiario::DEPENDENTE,
        cpfBeneficiario:        $cpf,
        codigoTipoRendimento:   isset($exclusiva->elab_tipo_rend_exclusiva->codigo)
                                    ? str_pad($exclusiva->elab_tipo_rend_exclusiva->codigo, 4, '0', STR_PAD_LEFT)
                                    : '0006',
        cnpjFontePagadora:      new Cnpj($exclusiva->cpf_cnpj),
        nomeFontePagadora:      mb_strtoupper($exclusiva->nome ?? ''),
        valorRendimento:        converterMonetario($exclusiva->valor ?? '0'),
    ));
}

// =============================================================================
// Seção: Investimentos no Exterior — Reg 37 (detalhes vinculados ao Reg 27)
// Gerado automaticamente para cada bem com grupo 07 (Ativos no Exterior / Lei 14.754/2023).
// Cada bem exterior gera até 2 registros: sub-tipo 1 (aplic. financeiras) e
// sub-tipo 2 (lucros/dividendos), quando os valores respectivos forem não-nulos.
// =============================================================================

$indiceBemInvest = 0;
foreach ($elab->elab_bens ?? [] as $bemInvest) {
    $indiceBemInvest++;
    $grupoInvest = str_pad(
        (string) ($bemInvest->elab_tipo_ben->grupo_codigo ?? '01'),
        2, '0', STR_PAD_LEFT
    );
    if ($grupoInvest !== '07') {
        continue;
    }

    $idBem = str_pad((string) $indiceBemInvest, 5, '0', STR_PAD_LEFT);
    $codigoItemInvest = str_pad(
        (string) ($bemInvest->elab_tipo_ben->codigo ?? '99'),
        2, '0', STR_PAD_LEFT
    );

    $aplRend   = converterMonetario($bemInvest->aplic_financ_rend_perda   ?? '0');
    $aplImp    = converterMonetario($bemInvest->aplic_financ_imp_exterior  ?? '0');
    $lucRend   = converterMonetario($bemInvest->lucros_div_valor_recebido  ?? '0');
    $lucImp    = converterMonetario($bemInvest->lucros_div_imposto_pago    ?? '0');

    // Sub-tipo 1: Aplicações Financeiras
    if ($aplRend->centavos > 0 || $aplImp->centavos > 0) {
        $declaracao->adicionarInvestimentoExterior(new RegistroInvestExteriorDTO(
            cpf:                $cpf,
            idBem:              $idBem,
            sequencialDetalhe:  '00001',
            subTipo:            SubTipoInvestimento::APLICACOES_FINANCEIRAS,
            rendimentoValor:    $aplRend,
            impostoPagoExterior: $aplImp,
            grupoBem:           $grupoInvest,
            codigoItem:         $codigoItemInvest,
        ));
    }

    // Sub-tipo 2: Lucros e Dividendos
    if ($lucRend->centavos > 0 || $lucImp->centavos > 0) {
        $declaracao->adicionarInvestimentoExterior(new RegistroInvestExteriorDTO(
            cpf:                $cpf,
            idBem:              $idBem,
            sequencialDetalhe:  '00002',
            subTipo:            SubTipoInvestimento::LUCROS_DIVIDENDOS,
            rendimentoValor:    $lucRend,
            impostoPagoExterior: $lucImp,
            grupoBem:           $grupoInvest,
            codigoItem:         $codigoItemInvest,
        ));
    }
}

// =============================================================================
// Seção: Saída Definitiva do País (registro 39)
// Equivalente: MapeadorSaidaDefinitiva
// =============================================================================

if ($saida !== null) {
    $declaracao->saidaDefinitiva = new RegistroSaidaDefinitivaDTO(
        cpf:                $cpf,
        cpfProcurador:      !empty($saida->cpf)
                                ? resolverCpf($saida->cpf)
                                : null,
        nomeProcurador:     mb_strtoupper($saida->nome     ?? ''),
        enderecoProcurador: mb_strtoupper($saida->endereco ?? ''),
        dataNaoResidente:   converterData($saida->dt_nao_residente ?? null),
        dataResidentePais:  converterData($saida->dt_residente     ?? null),
        codigoPaisDestino:  str_pad(
                                preg_replace('/\D/', '', $saida->pais->descricao ?? '0'),
                                3, '0', STR_PAD_LEFT
                            ),
    );
}

// =============================================================================
// Trailer (registro T9) — calculado após todas as seções
// Equivalente: MapeadorDeclaracaoService::gerarTrailer()
// =============================================================================

$totalRegistros = 2; // header + dados pessoais
$totalRegistros += count($declaracao->obterRendimentosPJ());
$totalRegistros += count($declaracao->obterRendimentosMensais());
$totalRegistros += count($declaracao->obterImpostosPagos());
$totalRegistros += count($declaracao->obterDeducoesLegais());
$totalRegistros += count($declaracao->obterDependentes());
$totalRegistros += count($declaracao->obterPagamentos());
$totalRegistros += count($declaracao->obterBensDireitos());
$totalRegistros += count($declaracao->obterDividas());
$totalRegistros += count($declaracao->obterRras());
$totalRegistros += count($declaracao->obterExigibilidadesSuspensas());
$totalRegistros += count($declaracao->obterRendimentosIsentos84());
$totalRegistros += count($declaracao->obterTribExclusivas());
$totalRegistros += count($declaracao->obterInvestimentosExterior());
$totalRegistros += $declaracao->saidaDefinitiva !== null ? 1 : 0;
$totalRegistros += 1; // o próprio trailer

$declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf, totalRegistros: $totalRegistros);

// =============================================================================
// Gera o arquivo .DBK
// =============================================================================

$gerador  = new GeradorDbk();
$nomeador = new NomeadorArquivo();

$nomeArquivo = $nomeador->gerarDeDeclaracao($declaracao);
$destino     = __DIR__ . '/output/' . $nomeArquivo;

$gerador->gerarParaArquivo($declaracao, $destino);

echo "Arquivo gerado: {$destino}\n";
echo "Nome: {$nomeArquivo}\n";
echo "Registros: {$totalRegistros}\n";
