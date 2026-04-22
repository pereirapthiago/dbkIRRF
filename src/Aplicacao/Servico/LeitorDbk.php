<?php

declare(strict_types=1);

namespace DbkIrrf\Aplicacao\Servico;

use DbkIrrf\Aplicacao\Fabrica\FabricaRegistro;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\DTO\RegistroExigibilidadeSuspensaDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento84DTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento86DTO;
use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\DTO\RegistroSaidaDefinitivaDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class LeitorDbk
{
    private FabricaRegistro $fabrica;

    public function __construct(?FabricaRegistro $fabrica = null)
    {
        $this->fabrica = $fabrica ?? new FabricaRegistro();
    }

    public function ler(string $conteudo): DeclaracaoDTO
    {
        $linhas = explode("\n", str_replace("\r\n", "\n", $conteudo));
        $declaracao = new DeclaracaoDTO();

        foreach ($linhas as $linha) {
            $linha = rtrim($linha, "\r");

            if (trim($linha) === '') {
                continue;
            }

            $tipo = TipoRegistro::identificarPorLinha($linha);

            if ($tipo === null) {
                continue;
            }

            $leitor = $this->fabrica->criarLeitor($tipo);
            $registro = $leitor->ler($linha);

            match ($tipo) {
                TipoRegistro::HEADER => $declaracao->header = $this->como($registro, RegistroHeaderDTO::class),
                TipoRegistro::DADOS_PESSOAIS => $declaracao->dadosPessoais = $this->como($registro, RegistroDadosPessoaisDTO::class),
                TipoRegistro::RENDIMENTOS_PJ => $declaracao->adicionarRendimentoPJ($this->como($registro, RegistroRendimentosPJDTO::class)),
                TipoRegistro::RENDIMENTOS_MENSAIS => $declaracao->adicionarRendimentoMensal($this->como($registro, RegistroRendimentosMensaisDTO::class)),
                TipoRegistro::IMPOSTO_PAGO => $declaracao->adicionarImpostoPago($this->como($registro, RegistroImpostoPagoDTO::class)),
                TipoRegistro::DEDUCAO_LEGAL => $declaracao->adicionarDeducaoLegal($this->como($registro, RegistroDeducaoLegalDTO::class)),
                TipoRegistro::DEPENDENTE => $declaracao->adicionarDependente($this->como($registro, RegistroDependenteDTO::class)),
                TipoRegistro::PAGAMENTO => $declaracao->adicionarPagamento($this->como($registro, RegistroPagamentoDTO::class)),
                TipoRegistro::BEM_DIREITO => $declaracao->adicionarBemDireito($this->como($registro, RegistroBemDireitoDTO::class)),
                TipoRegistro::DIVIDA => $declaracao->adicionarDivida($this->como($registro, RegistroDividaDTO::class)),
                TipoRegistro::INVESTIMENTO_EXTERIOR => $declaracao->adicionarInvestimentoExterior($this->como($registro, RegistroInvestExteriorDTO::class)),
                TipoRegistro::SAIDA_DEFINITIVA => $declaracao->saidaDefinitiva = $this->como($registro, RegistroSaidaDefinitivaDTO::class),
                TipoRegistro::RRA => $declaracao->adicionarRra($this->como($registro, RegistroRraDTO::class)),
                TipoRegistro::EXIGIBILIDADE_SUSPENSA => $declaracao->adicionarExigibilidadeSuspensa($this->como($registro, RegistroExigibilidadeSuspensaDTO::class)),
                TipoRegistro::RENDIMENTO_ISENTO => $declaracao->adicionarRendimentoIsento84($this->como($registro, RegistroRendimentoIsento84DTO::class)),
                TipoRegistro::RENDIMENTO_ISENTO_OUTROS => $declaracao->adicionarRendimentoIsento86($this->como($registro, RegistroRendimentoIsento86DTO::class)),
                TipoRegistro::TRIBUTACAO_EXCLUSIVA => $declaracao->adicionarTribExclusiva($this->como($registro, RegistroTribExclusivaDTO::class)),
                TipoRegistro::TRAILER => $declaracao->trailer = $this->como($registro, RegistroTrailerDTO::class),
            };
        }

        return $declaracao;
    }

    public function lerDeArquivo(string $caminhoArquivo): DeclaracaoDTO
    {
        if (!file_exists($caminhoArquivo)) {
            throw new \RuntimeException("Arquivo nao encontrado: {$caminhoArquivo}");
        }

        $conteudo = file_get_contents($caminhoArquivo);

        return $this->ler($conteudo);
    }

    /**
     * @template T
     * @param T $classe
     * @return T
     */
    private function como(object $registro, string $classe): object
    {
        if (!$registro instanceof $classe) {
            throw new \RuntimeException("Esperado {$classe}, recebido " . get_class($registro));
        }

        return $registro;
    }
}
