<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro 24 - Deducoes Legais - 40 caracteres.
 */
final class GeradorDeducaoLegal extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DEDUCAO_LEGAL;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroDeducaoLegalDTO) {
            throw new \InvalidArgumentException('Esperado RegistroDeducaoLegalDTO');
        }

        $r = $registro;
        $l = RegistroDeducaoLegalDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('24', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'codigoDeducao' => $this->numero($r->codigoDeducao, $l->campo('codigoDeducao')->tamanho),
            'valor' => $this->monetario($r->valor, $l->campo('valor')->tamanho),
        ]);
    }
}
