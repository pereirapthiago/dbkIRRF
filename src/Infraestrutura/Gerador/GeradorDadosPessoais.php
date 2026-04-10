<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro 16 - Dados Pessoais - 930 caracteres.
 * Posicoes conforme ESTRUTURA_DBK_IRPF.md
 */
final class GeradorDadosPessoais extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DADOS_PESSOAIS;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroDadosPessoaisDTO) {
            throw new \InvalidArgumentException('Esperado RegistroDadosPessoaisDTO');
        }

        $r = $registro;
        $l = RegistroDadosPessoaisDTO::obterLayout();
        $ehRetificadora = $r->tipoDeclaracao === TipoDeclaracao::RETIFICADORA;
        $flagRetif = $ehRetificadora ? 'S' : 'N';

        return $l->montarLinha([
            'tipoRegistro' => $this->numero('16', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'nome' => $this->texto($r->nome, $l->campo('nome')->tamanho),
            'tipoLogradouro' => $this->texto($r->tipoLogradouro, $l->campo('tipoLogradouro')->tamanho),
            'logradouro' => $this->texto($r->logradouro, $l->campo('logradouro')->tamanho),
            'numero' => $this->texto($r->numero, $l->campo('numero')->tamanho),
            'complemento' => $this->texto($r->complemento, $l->campo('complemento')->tamanho),
            'separadorComplementoBairro' => ' ',
            'bairro' => $this->texto($r->bairro, $l->campo('bairro')->tamanho),
            'cep' => $r->cep !== '00000000' && $r->cep !== ''
                ? $this->numero($r->cep, $l->campo('cep')->tamanho)
                : $this->espacos($l->campo('cep')->tamanho),
            'codigoMunicipioIbge' => $this->numero($r->codigoMunicipioIbge, $l->campo('codigoMunicipioIbge')->tamanho),
            'municipio' => $this->texto($r->municipio, $l->campo('municipio')->tamanho),
            'uf' => $r->uf !== null ? $r->uf->value : $this->espacos(2),
            'reservadoCodigo' => $this->texto($r->reservadoCodigo, $l->campo('reservadoCodigo')->tamanho),
            'email' => $this->texto($r->email, $l->campo('email')->tamanho),
            'cpfConjuge' => $this->cpfOuEspacos($r->cpfConjuge, $l->campo('cpfConjuge')->tamanho),
            'dddFixo' => $r->dddFixo !== ''
                ? $this->numero($r->dddFixo, $l->campo('dddFixo')->tamanho)
                : $this->espacos($l->campo('dddFixo')->tamanho),
            'dataNascimento' => $this->data($r->dataNascimento),
            'flagPos381' => $r->flagPos381,
            'codigoOcupacao' => $r->codigoOcupacao !== '000' && $r->codigoOcupacao !== ''
                ? $this->numero($r->codigoOcupacao, $l->campo('codigoOcupacao')->tamanho)
                : $this->espacos($l->campo('codigoOcupacao')->tamanho),
            'naturezaOcupacao' => $r->naturezaOcupacao,
            'flagSN387' => $r->flagSN387,
            'flagNS388' => $r->flagNS388,
            'flagRetificadora' => $flagRetif,
            'flagNS390' => $r->flagNS390,
            'flagAlteracaoCadastral' => $r->flagAlteracaoCadastral->value,
            'reciboRetificadora' => $ehRetificadora
                ? $this->numero($r->reciboDeclaracaoAnterior, $l->campo('reciboRetificadora')->tamanho)
                : $this->espacos($l->campo('reciboRetificadora')->tamanho),
            'reciboOriginal' => !$ehRetificadora && $r->reciboDeclaracaoAnterior !== ''
                ? $this->numero($r->reciboDeclaracaoAnterior, $l->campo('reciboOriginal')->tamanho)
                : $this->espacos($l->campo('reciboOriginal')->tamanho),
            'flagA454' => $r->flagA454,
            'dddCelular' => $r->dddCelular !== ''
                ? $this->numero($r->dddCelular, $l->campo('dddCelular')->tamanho)
                : $this->espacos($l->campo('dddCelular')->tamanho),
            'celular' => $r->celular !== ''
                ? $this->numero($r->celular, $l->campo('celular')->tamanho)
                : $this->espacos($l->campo('celular')->tamanho),
            'flagPossuiConjuge' => $r->flagPossuiConjuge->value,
            'telefoneFixo' => $r->telefoneFixo !== ''
                ? $this->numero($r->telefoneFixo, $l->campo('telefoneFixo')->tamanho)
                : $this->espacos($l->campo('telefoneFixo')->tamanho),
            'camposAdicionaisRaw' => $this->rawOuEspacos($r->camposAdicionaisRaw, $l->campo('camposAdicionaisRaw')->tamanho),
            'flagResidenciaPais' => $r->flagResidenciaPais,
            'dataResidenciaPais' => $this->data($r->dataResidenciaPais),
        ]);
    }
}
