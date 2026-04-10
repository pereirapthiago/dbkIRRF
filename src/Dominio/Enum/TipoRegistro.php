<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\Enum;

enum TipoRegistro: string
{
    case HEADER = 'IRPF';
    case DADOS_PESSOAIS = '16';
    case RENDIMENTOS_PJ = '21';
    case RENDIMENTOS_MENSAIS = '22';
    case IMPOSTO_PAGO = '23';
    case DEDUCAO_LEGAL = '24';
    case DEPENDENTE = '25';
    case PAGAMENTO = '26';
    case BEM_DIREITO = '27';
    case DIVIDA = '28';
    case INVESTIMENTO_EXTERIOR = '37';
    case SAIDA_DEFINITIVA = '39';
    case RRA = '45';
    case EXIGIBILIDADE_SUSPENSA = '80';
    case RENDIMENTO_ISENTO = '84';
    case TRIBUTACAO_EXCLUSIVA = '88';
    case TRAILER = 'T9';

    public function obterDescricao(): string
    {
        return match ($this) {
            self::HEADER => 'Header / Cabecalho da declaracao',
            self::DADOS_PESSOAIS => 'Dados pessoais do contribuinte',
            self::RENDIMENTOS_PJ => 'Rendimentos tributaveis recebidos de PJ',
            self::RENDIMENTOS_MENSAIS => 'Rendimentos mensais PF/Exterior (carne-leao)',
            self::IMPOSTO_PAGO => 'Imposto pago / retido por codigo',
            self::DEDUCAO_LEGAL => 'Deducoes legais por codigo',
            self::DEPENDENTE => 'Dependentes',
            self::PAGAMENTO => 'Pagamentos efetuados',
            self::BEM_DIREITO => 'Bens e direitos',
            self::DIVIDA => 'Dividas e onus reais',
            self::INVESTIMENTO_EXTERIOR => 'Detalhe investimento exterior (Lei 14.754/2023)',
            self::SAIDA_DEFINITIVA => 'Saida definitiva do pais',
            self::RRA => 'Rendimentos Recebidos Acumuladamente (RRA)',
            self::EXIGIBILIDADE_SUSPENSA => 'Rendimento com exigibilidade suspensa',
            self::RENDIMENTO_ISENTO => 'Rendimentos isentos e nao tributaveis',
            self::TRIBUTACAO_EXCLUSIVA => 'Rendimentos sujeitos a tributacao exclusiva/definitiva',
            self::TRAILER => 'Trailer / Totalizador',
        };
    }

    public function obterTamanhoLinha(): int
    {
        return match ($this) {
            self::HEADER => 1244,
            self::DADOS_PESSOAIS => 930,
            self::RENDIMENTOS_PJ => 170,
            self::RENDIMENTOS_MENSAIS => 167,
            self::IMPOSTO_PAGO => 40,
            self::DEDUCAO_LEGAL => 40,
            self::DEPENDENTE => 224,
            self::PAGAMENTO => 671,
            self::BEM_DIREITO => 1251,
            self::DIVIDA => 576,
            self::INVESTIMENTO_EXTERIOR => 103,
            self::SAIDA_DEFINITIVA => 193,
            self::RRA => 216,
            self::EXIGIBILIDADE_SUSPENSA => 123,
            self::RENDIMENTO_ISENTO => 144,
            self::TRIBUTACAO_EXCLUSIVA => 131,
            self::TRAILER => 449,
        };
    }

    public function obterTamanhoCodigo(): int
    {
        return match ($this) {
            self::HEADER => 4,
            default => 2,
        };
    }

    public static function identificarPorLinha(string $linha): ?self
    {
        $prefixo4 = substr($linha, 0, 4);
        if ($prefixo4 === 'IRPF') {
            return self::HEADER;
        }

        $prefixo2 = substr($linha, 0, 2);

        return self::tryFrom($prefixo2);
    }
}
