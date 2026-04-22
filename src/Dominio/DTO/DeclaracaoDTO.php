<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Enum\ModalidadeDeclaracao;

/**
 * DTO principal que agrega todos os registros de uma declaracao IRPF.
 * Nao e readonly pois e preenchido incrementalmente durante a leitura/montagem.
 */
final class DeclaracaoDTO
{
    public ModalidadeDeclaracao $modalidade = ModalidadeDeclaracao::ANUAL;
    public ?RegistroHeaderDTO $header = null;
    public ?RegistroDadosPessoaisDTO $dadosPessoais = null;
    public ?RegistroTrailerDTO $trailer = null;

    // Sprint 3 - Registros Financeiros
    /** @var list<RegistroRendimentosPJDTO> */
    private array $rendimentosPJ = [];
    /** @var list<RegistroRendimentosMensaisDTO> */
    private array $rendimentosMensais = [];
    /** @var list<RegistroImpostoPagoDTO> */
    private array $impostosPagos = [];
    /** @var list<RegistroDeducaoLegalDTO> */
    private array $deducoesLegais = [];

    // Sprint 4 - Registros Complementares
    /** @var list<RegistroDependenteDTO> */
    private array $dependentes = [];
    /** @var list<RegistroPagamentoDTO> */
    private array $pagamentos = [];
    /** @var list<RegistroBemDireitoDTO> */
    private array $bensDireitos = [];
    /** @var list<RegistroDividaDTO> */
    private array $dividas = [];
    /** @var list<RegistroRraDTO> */
    private array $rras = [];
    /** @var list<RegistroRendimentoIsento84DTO> */
    private array $rendimentosIsentos84 = [];
    /** @var list<RegistroRendimentoIsento86DTO> */
    private array $rendimentosIsentos86 = [];
    /** @var list<RegistroExigibilidadeSuspensaDTO> */
    private array $exigibilidadesSuspensas = [];
    /** @var list<RegistroTribExclusivaDTO> */
    private array $tribExclusivas = [];
    /** @var list<RegistroInvestExteriorDTO> */
    private array $investimentosExterior = [];

    // Registro 39 - Saida Definitiva
    public ?RegistroSaidaDefinitivaDTO $saidaDefinitiva = null;

    // === Rendimentos PJ (Reg 21) ===

    public function adicionarRendimentoPJ(RegistroRendimentosPJDTO $dto): void
    {
        $this->rendimentosPJ[] = $dto;
    }

    /** @return list<RegistroRendimentosPJDTO> */
    public function obterRendimentosPJ(): array
    {
        return $this->rendimentosPJ;
    }

    // === Rendimentos Mensais (Reg 22) ===

    public function adicionarRendimentoMensal(RegistroRendimentosMensaisDTO $dto): void
    {
        $this->rendimentosMensais[] = $dto;
    }

    /** @return list<RegistroRendimentosMensaisDTO> */
    public function obterRendimentosMensais(): array
    {
        return $this->rendimentosMensais;
    }

    // === Impostos Pagos (Reg 23) ===

    public function adicionarImpostoPago(RegistroImpostoPagoDTO $dto): void
    {
        $this->impostosPagos[] = $dto;
    }

    /** @return list<RegistroImpostoPagoDTO> */
    public function obterImpostosPagos(): array
    {
        return $this->impostosPagos;
    }

    // === Deducoes Legais (Reg 24) ===

    public function adicionarDeducaoLegal(RegistroDeducaoLegalDTO $dto): void
    {
        $this->deducoesLegais[] = $dto;
    }

    /** @return list<RegistroDeducaoLegalDTO> */
    public function obterDeducoesLegais(): array
    {
        return $this->deducoesLegais;
    }

    // === Dependentes (Reg 25) ===

    public function adicionarDependente(RegistroDependenteDTO $dto): void
    {
        $this->dependentes[] = $dto;
    }

    /** @return list<RegistroDependenteDTO> */
    public function obterDependentes(): array
    {
        return $this->dependentes;
    }

    // === Pagamentos (Reg 26) ===

    public function adicionarPagamento(RegistroPagamentoDTO $dto): void
    {
        $this->pagamentos[] = $dto;
    }

    /** @return list<RegistroPagamentoDTO> */
    public function obterPagamentos(): array
    {
        return $this->pagamentos;
    }

    // === Bens e Direitos (Reg 27) ===

    public function adicionarBemDireito(RegistroBemDireitoDTO $dto): void
    {
        $this->bensDireitos[] = $dto;
    }

    /** @return list<RegistroBemDireitoDTO> */
    public function obterBensDireitos(): array
    {
        return $this->bensDireitos;
    }

    // === Dividas (Reg 28) ===

    public function adicionarDivida(RegistroDividaDTO $dto): void
    {
        $this->dividas[] = $dto;
    }

    /** @return list<RegistroDividaDTO> */
    public function obterDividas(): array
    {
        return $this->dividas;
    }

    // === Rendimentos Recebidos Acumuladamente (Reg 45) ===

    public function adicionarRra(RegistroRraDTO $dto): void
    {
        $this->rras[] = $dto;
    }

    /** @return list<RegistroRraDTO> */
    public function obterRras(): array
    {
        return $this->rras;
    }

    // === Rendimentos Isentos (Reg 84) ===

    public function adicionarRendimentoIsento84(RegistroRendimentoIsento84DTO $dto): void
    {
        $this->rendimentosIsentos84[] = $dto;
    }

    /** @return list<RegistroRendimentoIsento84DTO> */
    public function obterRendimentosIsentos84(): array
    {
        return $this->rendimentosIsentos84;
    }

    // === Rendimentos Isentos Outros (Reg 86) ===

    public function adicionarRendimentoIsento86(RegistroRendimentoIsento86DTO $dto): void
    {
        $this->rendimentosIsentos86[] = $dto;
    }

    /** @return list<RegistroRendimentoIsento86DTO> */
    public function obterRendimentosIsentos86(): array
    {
        return $this->rendimentosIsentos86;
    }

    // === Exigibilidade Suspensa (Reg 80) ===

    public function adicionarExigibilidadeSuspensa(RegistroExigibilidadeSuspensaDTO $dto): void
    {
        $this->exigibilidadesSuspensas[] = $dto;
    }

    /** @return list<RegistroExigibilidadeSuspensaDTO> */
    public function obterExigibilidadesSuspensas(): array
    {
        return $this->exigibilidadesSuspensas;
    }

    // === Trib Exclusiva (Reg 88) ===

    public function adicionarTribExclusiva(RegistroTribExclusivaDTO $dto): void
    {
        $this->tribExclusivas[] = $dto;
    }

    /** @return list<RegistroTribExclusivaDTO> */
    public function obterTribExclusivas(): array
    {
        return $this->tribExclusivas;
    }

    // === Investimentos Exterior (Reg 37) ===

    public function adicionarInvestimentoExterior(RegistroInvestExteriorDTO $dto): void
    {
        $this->investimentosExterior[] = $dto;
    }

    /** @return list<RegistroInvestExteriorDTO> */
    public function obterInvestimentosExterior(): array
    {
        return $this->investimentosExterior;
    }
}
