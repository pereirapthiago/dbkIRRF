<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro 22 - Rendimentos Mensais PF/Exterior - 167 caracteres.
 */
final class GeradorRendimentosMensais extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTOS_MENSAIS;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroRendimentosMensaisDTO) {
            throw new \InvalidArgumentException('Esperado RegistroRendimentosMensaisDTO');
        }

        $r = $registro;
        $l = RegistroRendimentosMensaisDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('22', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'flagNS' => $r->flagNS,
            'mesReferencia' => $this->numero($r->mesReferencia, $l->campo('mesReferencia')->tamanho),
            'rendNaoAssalariado' => $this->monetario($r->rendNaoAssalariado, $l->campo('rendNaoAssalariado')->tamanho),
            'temporada' => $this->monetario($r->temporada, $l->campo('temporada')->tamanho),
            'outrosRendimentos' => $this->monetario($r->outrosRendimentos, $l->campo('outrosRendimentos')->tamanho),
            'exterior' => $this->monetario($r->exterior, $l->campo('exterior')->tamanho),
            'previdencia' => $this->monetario($r->previdencia, $l->campo('previdencia')->tamanho),
            'dependentes' => $this->monetario($r->dependentes, $l->campo('dependentes')->tamanho),
            'pensaoAlimenticia' => $this->monetario($r->pensaoAlimenticia, $l->campo('pensaoAlimenticia')->tamanho),
            'livroCaixa' => $this->monetario($r->livroCaixa, $l->campo('livroCaixa')->tamanho),
            'totalRendimentosMes' => $this->monetario($r->totalRendimentosMes, $l->campo('totalRendimentosMes')->tamanho),
            'darfPago' => $this->monetario($r->darfPago, $l->campo('darfPago')->tamanho),
        ]);
    }
}
