<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento86DTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorRendimentoIsento86 extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTO_ISENTO_OUTROS;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroRendimentoIsento86DTO) {
            throw new \InvalidArgumentException('Esperado RegistroRendimentoIsento86DTO');
        }

        $r = $registro;
        $l = RegistroRendimentoIsento86DTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('86', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'tipoBeneficiario' => $r->tipoBeneficiario->value,
            'cpfBeneficiario' => $r->cpfBeneficiario->valor,
            'codigoTipoRendimento' => $this->numero($r->codigoTipoRendimento, $l->campo('codigoTipoRendimento')->tamanho),
            'cnpjFontePagadora' => $this->texto($r->cnpjFontePagadora, $l->campo('cnpjFontePagadora')->tamanho),
            'nomeFontePagadora' => $this->texto($r->nomeFontePagadora, $l->campo('nomeFontePagadora')->tamanho),
            'valorRendimentoIsento' => $this->monetario($r->valorRendimentoIsento, $l->campo('valorRendimentoIsento')->tamanho),
            'descricaoLivre' => $this->texto($r->descricaoLivre, $l->campo('descricaoLivre')->tamanho),
        ]);
    }
}
