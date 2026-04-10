<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorInvestExterior extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::INVESTIMENTO_EXTERIOR;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroInvestExteriorDTO) {
            throw new \InvalidArgumentException('Esperado RegistroInvestExteriorDTO');
        }

        $r = $registro;
        $l = RegistroInvestExteriorDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('37', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'idBem' => $this->numero($r->idBem, $l->campo('idBem')->tamanho),
            'sequencialDetalhe' => $this->numero($r->sequencialDetalhe, $l->campo('sequencialDetalhe')->tamanho),
            'subTipo' => $r->subTipo->value,
            'rendimentoValor' => $this->monetario($r->rendimentoValor, $l->campo('rendimentoValor')->tamanho),
            'impostoDevido15' => $this->monetario($r->impostoDevido15, $l->campo('impostoDevido15')->tamanho),
            'impostoPagoExterior' => $this->monetario($r->impostoPagoExterior, $l->campo('impostoPagoExterior')->tamanho),
            'campoMonetario4' => $this->monetario($r->campoMonetario4, $l->campo('campoMonetario4')->tamanho),
            'campoMonetario5' => $this->monetario($r->campoMonetario5, $l->campo('campoMonetario5')->tamanho),
            'grupoBem' => $this->numero($r->grupoBem, $l->campo('grupoBem')->tamanho),
            'codigoItem' => $this->numero($r->codigoItem, $l->campo('codigoItem')->tamanho),
        ]);
    }
}
