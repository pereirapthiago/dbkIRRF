<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento84DTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorRendimentoIsento84 extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTO_ISENTO;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroRendimentoIsento84DTO) {
            throw new \InvalidArgumentException('Esperado RegistroRendimentoIsento84DTO');
        }

        $r = $registro;
        $l = RegistroRendimentoIsento84DTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('84', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'tipoBeneficiario' => $r->tipoBeneficiario->value,
            'cpfBeneficiario' => $r->cpfBeneficiario->valor,
            'codigoTipoRendimento' => $this->numero($r->codigoTipoRendimento, $l->campo('codigoTipoRendimento')->tamanho),
            'cnpjFontePagadora' => $this->texto($r->cnpjFontePagadora, $l->campo('cnpjFontePagadora')->tamanho),
            'nomeFontePagadora' => $this->texto($r->nomeFontePagadora, $l->campo('nomeFontePagadora')->tamanho),
            'valorRendimentoIsento' => $this->monetario($r->valorRendimentoIsento, $l->campo('valorRendimentoIsento')->tamanho),
            'valorAdicional' => $this->monetario($r->valorAdicional, $l->campo('valorAdicional')->tamanho),
        ]);
    }
}
