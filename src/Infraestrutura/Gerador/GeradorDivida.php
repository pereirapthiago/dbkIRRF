<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorDivida extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DIVIDA;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroDividaDTO) {
            throw new \InvalidArgumentException('Esperado RegistroDividaDTO');
        }

        $r = $registro;
        $l = RegistroDividaDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('28', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'codigoDivida' => $this->numero($r->codigoDivida, $l->campo('codigoDivida')->tamanho),
            'descricao' => $this->texto($r->descricao, $l->campo('descricao')->tamanho),
            'saldoAnterior' => $this->monetario($r->saldoAnterior, $l->campo('saldoAnterior')->tamanho),
            'saldoAtual' => $this->monetario($r->saldoAtual, $l->campo('saldoAtual')->tamanho),
            'valorPagoAno' => $this->monetario($r->valorPagoAno, $l->campo('valorPagoAno')->tamanho),
        ]);
    }
}
