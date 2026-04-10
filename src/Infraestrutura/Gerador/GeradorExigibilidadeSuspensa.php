<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroExigibilidadeSuspensaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorExigibilidadeSuspensa extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::EXIGIBILIDADE_SUSPENSA;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroExigibilidadeSuspensaDTO) {
            throw new \InvalidArgumentException('Esperado RegistroExigibilidadeSuspensaDTO');
        }

        $r = $registro;
        $l = RegistroExigibilidadeSuspensaDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('80', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'cnpjFontePagadora' => $r->cnpjFontePagadora->valor,
            'nomeFontePagadora' => $this->texto($r->nomeFontePagadora, $l->campo('nomeFontePagadora')->tamanho),
            'rendimentosTributaveis' => $this->monetario($r->rendimentosTributaveis, $l->campo('rendimentosTributaveis')->tamanho),
            'depositosJudiciais' => $this->monetario($r->depositosJudiciais, $l->campo('depositosJudiciais')->tamanho),
        ]);
    }
}
