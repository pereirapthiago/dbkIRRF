<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorTribExclusiva extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::TRIBUTACAO_EXCLUSIVA;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroTribExclusivaDTO) {
            throw new \InvalidArgumentException('Esperado RegistroTribExclusivaDTO');
        }

        $r = $registro;
        $l = RegistroTribExclusivaDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('88', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'tipoBeneficiario' => $r->tipoBeneficiario->value,
            'cpfBeneficiario' => $r->cpfBeneficiario->valor,
            'codigoTipoRendimento' => $this->numero($r->codigoTipoRendimento, $l->campo('codigoTipoRendimento')->tamanho),
            'cnpjFontePagadora' => $r->cnpjFontePagadora->valor,
            'nomeFontePagadora' => $this->texto($r->nomeFontePagadora, $l->campo('nomeFontePagadora')->tamanho),
            'valorRendimento' => $this->monetario($r->valorRendimento, $l->campo('valorRendimento')->tamanho),
        ]);
    }
}
