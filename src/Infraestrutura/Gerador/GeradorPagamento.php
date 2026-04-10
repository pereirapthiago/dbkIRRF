<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorPagamento extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::PAGAMENTO;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroPagamentoDTO) {
            throw new \InvalidArgumentException('Esperado RegistroPagamentoDTO');
        }

        $r = $registro;
        $l = RegistroPagamentoDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('26', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'codigoPagamento' => $this->numero($r->codigoPagamento, $l->campo('codigoPagamento')->tamanho),
            'cpfCnpjBeneficiario' => $this->texto($r->cpfCnpjBeneficiario, $l->campo('cpfCnpjBeneficiario')->tamanho),
            'nomeBeneficiario' => $this->texto($r->nomeBeneficiario, $l->campo('nomeBeneficiario')->tamanho),
            'valorPago' => $this->monetario($r->valorPago, $l->campo('valorPago')->tamanho),
            'parcelaNaoDedutivel' => $this->monetario($r->parcelaNaoDedutivel, $l->campo('parcelaNaoDedutivel')->tamanho),
            'sequencial' => $r->sequencial,
            'flagTitularDependente' => $r->flagTitularDependente,
            'descricao' => $this->texto($r->descricao, $l->campo('descricao')->tamanho),
            'codigoPais' => $this->numero($r->codigoPais, $l->campo('codigoPais')->tamanho),
        ]);
    }
}
