<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro 21 - Rendimentos Tributaveis PJ - 170 caracteres.
 */
final class GeradorRendimentosPJ extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTOS_PJ;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroRendimentosPJDTO) {
            throw new \InvalidArgumentException('Esperado RegistroRendimentosPJDTO');
        }

        $r = $registro;
        $l = RegistroRendimentosPJDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('21', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'cnpjFontePagadora' => $r->cnpjFontePagadora->valor,
            'nomeFontePagadora' => $this->texto($r->nomeFontePagadora, $l->campo('nomeFontePagadora')->tamanho),
            'rendimentosRecebidos' => $this->monetario($r->rendimentosRecebidos, $l->campo('rendimentosRecebidos')->tamanho),
            'contribPrevidenciaria' => $this->monetario($r->contribPrevidenciaria, $l->campo('contribPrevidenciaria')->tamanho),
            'decimoTerceiroSalario' => $this->monetario($r->decimoTerceiroSalario, $l->campo('decimoTerceiroSalario')->tamanho),
            'impostoRetidoFonte' => $this->monetario($r->impostoRetidoFonte, $l->campo('impostoRetidoFonte')->tamanho),
            'irrfDecimoTerceiro' => $this->monetario($r->irrfDecimoTerceiro, $l->campo('irrfDecimoTerceiro')->tamanho),
        ]);
    }
}
