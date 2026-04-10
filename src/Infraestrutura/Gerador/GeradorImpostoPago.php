<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro 23 - Imposto Pago/Retido - 40 caracteres.
 */
final class GeradorImpostoPago extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::IMPOSTO_PAGO;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroImpostoPagoDTO) {
            throw new \InvalidArgumentException('Esperado RegistroImpostoPagoDTO');
        }

        $r = $registro;
        $l = RegistroImpostoPagoDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('23', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'codigo' => $this->numero($r->codigo, $l->campo('codigo')->tamanho),
            'valor' => $this->monetario($r->valor, $l->campo('valor')->tamanho),
        ]);
    }
}
