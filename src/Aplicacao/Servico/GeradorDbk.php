<?php

declare(strict_types=1);

namespace DbkIrrf\Aplicacao\Servico;

use DbkIrrf\Aplicacao\Fabrica\FabricaRegistro;
use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorDbk
{
    private FabricaRegistro $fabrica;

    public function __construct(?FabricaRegistro $fabrica = null)
    {
        $this->fabrica = $fabrica ?? new FabricaRegistro();
    }

    public function gerar(DeclaracaoDTO $declaracao): string
    {
        $linhas = [];

        // Header (IRPF) - obrigatorio
        if ($declaracao->header !== null) {
            $linhas[] = $this->gerarLinha($declaracao->header);
        }

        // Dados Pessoais (Reg 16) - obrigatorio
        if ($declaracao->dadosPessoais !== null) {
            $linhas[] = $this->gerarLinha($declaracao->dadosPessoais);
        }

        // Rendimentos PJ (Reg 21)
        foreach ($declaracao->obterRendimentosPJ() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Rendimentos Mensais (Reg 22) - 12 meses
        foreach ($declaracao->obterRendimentosMensais() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Imposto Pago (Reg 23)
        foreach ($declaracao->obterImpostosPagos() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Deducoes Legais (Reg 24)
        foreach ($declaracao->obterDeducoesLegais() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Dependentes (Reg 25)
        foreach ($declaracao->obterDependentes() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Pagamentos (Reg 26)
        foreach ($declaracao->obterPagamentos() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Bens e Direitos (Reg 27)
        foreach ($declaracao->obterBensDireitos() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Dividas (Reg 28)
        foreach ($declaracao->obterDividas() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Investimentos Exterior (Reg 37)
        foreach ($declaracao->obterInvestimentosExterior() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Saida Definitiva (Reg 39)
        if ($declaracao->saidaDefinitiva !== null) {
            $linhas[] = $this->gerarLinha($declaracao->saidaDefinitiva);
        }

        // Rendimentos Recebidos Acumuladamente (Reg 45)
        foreach ($declaracao->obterRras() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Exigibilidade Suspensa (Reg 80)
        foreach ($declaracao->obterExigibilidadesSuspensas() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Rendimentos Isentos (Reg 84)
        foreach ($declaracao->obterRendimentosIsentos84() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Rendimentos Isentos Outros / cod 26 (Reg 86)
        foreach ($declaracao->obterRendimentosIsentos86() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Trib Exclusiva (Reg 88)
        foreach ($declaracao->obterTribExclusivas() as $dto) {
            $linhas[] = $this->gerarLinha($dto);
        }

        // Trailer (T9) - obrigatorio
        if ($declaracao->trailer !== null) {
            $linhas[] = $this->gerarLinha($declaracao->trailer);
        }

        return implode("\r\n", $linhas) . "\r\n";
    }

    public function gerarParaArquivo(DeclaracaoDTO $declaracao, string $caminhoArquivo): void
    {
        $conteudo = $this->gerar($declaracao);

        $diretorio = dirname($caminhoArquivo);
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        file_put_contents($caminhoArquivo, $conteudo);
    }

    private function gerarLinha(RegistroInterface $registro): string
    {
        $gerador = $this->fabrica->criarGerador($registro->obterTipo());

        return $gerador->gerar($registro);
    }
}
