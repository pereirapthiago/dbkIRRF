<?php

declare(strict_types=1);

namespace DbkIrrf\Aplicacao\Fabrica;

use DbkIrrf\Dominio\Contrato\GeradorRegistroInterface;
use DbkIrrf\Dominio\Contrato\LeitorRegistroInterface;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Infraestrutura\Gerador;
use DbkIrrf\Infraestrutura\Leitor;

final class FabricaRegistro
{
    public function criarGerador(TipoRegistro $tipo): GeradorRegistroInterface
    {
        return match ($tipo) {
            TipoRegistro::HEADER => new Gerador\GeradorHeader(),
            TipoRegistro::DADOS_PESSOAIS => new Gerador\GeradorDadosPessoais(),
            TipoRegistro::RENDIMENTOS_PJ => new Gerador\GeradorRendimentosPJ(),
            TipoRegistro::RENDIMENTOS_MENSAIS => new Gerador\GeradorRendimentosMensais(),
            TipoRegistro::IMPOSTO_PAGO => new Gerador\GeradorImpostoPago(),
            TipoRegistro::DEDUCAO_LEGAL => new Gerador\GeradorDeducaoLegal(),
            TipoRegistro::DEPENDENTE => new Gerador\GeradorDependente(),
            TipoRegistro::PAGAMENTO => new Gerador\GeradorPagamento(),
            TipoRegistro::BEM_DIREITO => new Gerador\GeradorBemDireito(),
            TipoRegistro::DIVIDA => new Gerador\GeradorDivida(),
            TipoRegistro::INVESTIMENTO_EXTERIOR => new Gerador\GeradorInvestExterior(),
            TipoRegistro::SAIDA_DEFINITIVA => new Gerador\GeradorSaidaDefinitiva(),
            TipoRegistro::RRA => new Gerador\GeradorRra(),
            TipoRegistro::EXIGIBILIDADE_SUSPENSA => new Gerador\GeradorExigibilidadeSuspensa(),
            TipoRegistro::RENDIMENTO_ISENTO => new Gerador\GeradorRendimentoIsento84(),
            TipoRegistro::RENDIMENTO_ISENTO_OUTROS => new Gerador\GeradorRendimentoIsento86(),
            TipoRegistro::TRIBUTACAO_EXCLUSIVA => new Gerador\GeradorTribExclusiva(),
            TipoRegistro::TRAILER => new Gerador\GeradorTrailer(),
        };
    }

    public function criarLeitor(TipoRegistro $tipo): LeitorRegistroInterface
    {
        return match ($tipo) {
            TipoRegistro::HEADER => new Leitor\LeitorHeader(),
            TipoRegistro::DADOS_PESSOAIS => new Leitor\LeitorDadosPessoais(),
            TipoRegistro::RENDIMENTOS_PJ => new Leitor\LeitorRendimentosPJ(),
            TipoRegistro::RENDIMENTOS_MENSAIS => new Leitor\LeitorRendimentosMensais(),
            TipoRegistro::IMPOSTO_PAGO => new Leitor\LeitorImpostoPago(),
            TipoRegistro::DEDUCAO_LEGAL => new Leitor\LeitorDeducaoLegal(),
            TipoRegistro::DEPENDENTE => new Leitor\LeitorDependente(),
            TipoRegistro::PAGAMENTO => new Leitor\LeitorPagamento(),
            TipoRegistro::BEM_DIREITO => new Leitor\LeitorBemDireito(),
            TipoRegistro::DIVIDA => new Leitor\LeitorDivida(),
            TipoRegistro::INVESTIMENTO_EXTERIOR => new Leitor\LeitorInvestExterior(),
            TipoRegistro::SAIDA_DEFINITIVA => new Leitor\LeitorSaidaDefinitiva(),
            TipoRegistro::RRA => new Leitor\LeitorRra(),
            TipoRegistro::EXIGIBILIDADE_SUSPENSA => new Leitor\LeitorExigibilidadeSuspensa(),
            TipoRegistro::RENDIMENTO_ISENTO => new Leitor\LeitorRendimentoIsento84(),
            TipoRegistro::RENDIMENTO_ISENTO_OUTROS => new Leitor\LeitorRendimentoIsento86(),
            TipoRegistro::TRIBUTACAO_EXCLUSIVA => new Leitor\LeitorTribExclusiva(),
            TipoRegistro::TRAILER => new Leitor\LeitorTrailer(),
        };
    }
}
