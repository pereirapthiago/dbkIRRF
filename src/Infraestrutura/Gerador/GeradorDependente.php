<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorDependente extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DEPENDENTE;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroDependenteDTO) {
            throw new \InvalidArgumentException('Esperado RegistroDependenteDTO');
        }

        $r = $registro;
        $l = RegistroDependenteDTO::obterLayout();

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('25', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'sequencial' => $this->numero($r->sequencial, $l->campo('sequencial')->tamanho),
            'tipoDependente' => $this->numero($r->tipoDependente, $l->campo('tipoDependente')->tamanho),
            'nomeDependente' => $this->texto($r->nomeDependente, $l->campo('nomeDependente')->tamanho),
            'dataNascimento' => $this->data($r->dataNascimento),
            'cpfDependente' => $this->cpfOuEspacos($r->cpfDependente),
            'desconhecido' => $this->rawOuEspacos($r->camposAdicionaisRaw, $l->campo('desconhecido')->tamanho),
            'moraTitular' => $r->moraTitular ? '1' : '0',
            'email' => $this->texto($r->email, $l->campo('email')->tamanho),
            'ddd' => $this->numero($r->ddd, $l->campo('ddd')->tamanho),
            'celular' => $this->numero($r->celular, $l->campo('celular')->tamanho),
            'tipoTelefone' => '2',
        ]);
    }
}
