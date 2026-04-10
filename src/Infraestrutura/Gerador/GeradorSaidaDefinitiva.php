<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroSaidaDefinitivaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorSaidaDefinitiva extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::SAIDA_DEFINITIVA;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroSaidaDefinitivaDTO) {
            throw new \InvalidArgumentException('Esperado RegistroSaidaDefinitivaDTO');
        }

        $r = $registro;
        $l = RegistroSaidaDefinitivaDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('39', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'cpfProcurador' => $this->cpfOuEspacos($r->cpfProcurador, $l->campo('cpfProcurador')->tamanho),
            'nomeProcurador' => $this->texto($r->nomeProcurador, $l->campo('nomeProcurador')->tamanho),
            'enderecoProcurador' => $this->texto($r->enderecoProcurador, $l->campo('enderecoProcurador')->tamanho),
            'dataNaoResidente' => $this->data($r->dataNaoResidente),
            'dataResidentePais' => $this->data($r->dataResidentePais),
            'codigoPaisDestino' => $this->numero($r->codigoPaisDestino, $l->campo('codigoPaisDestino')->tamanho),
        ]);
    }
}
