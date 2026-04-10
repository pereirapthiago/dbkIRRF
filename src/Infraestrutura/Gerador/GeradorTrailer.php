<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro T9 - Trailer/Totalizador - 449 caracteres.
 * Posicoes conforme ESTRUTURA_DBK_IRPF.md
 */
final class GeradorTrailer extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::TRAILER;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroTrailerDTO) {
            throw new \InvalidArgumentException('Esperado RegistroTrailerDTO');
        }

        $r = $registro;
        $l = RegistroTrailerDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->texto('T9', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'totalRegistros' => $this->numero($r->totalRegistros, $l->campo('totalRegistros')->tamanho),
            'contadoresRaw' => $this->rawOuZeros($r->contadoresRaw, $l->campo('contadoresRaw')->tamanho),
        ]);
    }
}
