<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorRra extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RRA;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroRraDTO) {
            throw new \InvalidArgumentException('Esperado RegistroRraDTO');
        }

        $r = $registro;
        $l = RegistroRraDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('45', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'cnpjFontePagadora' => $r->cnpjFontePagadora->valor,
            'nomeFontePagadora' => $this->texto($r->nomeFontePagadora, $l->campo('nomeFontePagadora')->tamanho),
            // zeros90 (pos 90-102): nao incluir — montarLinha preenche com zeros automaticamente
            'contribPrevidenciaria' => $this->monetario($r->contribPrevidenciaria, $l->campo('contribPrevidenciaria')->tamanho),
            'parcelaIsenta65Anos' => $this->monetario($r->parcelaIsenta65Anos, $l->campo('parcelaIsenta65Anos')->tamanho),
            'impostoRetidoFonte' => $this->monetario($r->impostoRetidoFonte, $l->campo('impostoRetidoFonte')->tamanho),
            'mesRecebimentoRRA' => $this->numero($r->mesRecebimentoRRA, $l->campo('mesRecebimentoRRA')->tamanho),
            'metadadosRRA' => $this->texto($r->metadadosRRA, $l->campo('metadadosRRA')->tamanho),
            'flagCodigo150' => $this->numero($r->flagCodigo150, $l->campo('flagCodigo150')->tamanho),
            'numMesesRRA' => $r->numMesesRRA,
            'reservadoPos154' => $r->reservadoPos154,
            'impostoBrutoRRA' => $this->monetario($r->impostoBrutoRRA, $l->campo('impostoBrutoRRA')->tamanho),
            'rendimentosRRA' => $this->monetario($r->rendimentosRRA, $l->campo('rendimentosRRA')->tamanho),
            'rendimentosRRACopia' => $this->monetario($r->rendimentosRRACopia, $l->campo('rendimentosRRACopia')->tamanho),
            'campoDesconhecido194' => $this->monetario($r->campoDesconhecido194, $l->campo('campoDesconhecido194')->tamanho),
        ]);
    }
}
