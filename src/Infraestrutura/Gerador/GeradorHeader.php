<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;

/**
 * Gerador do registro IRPF (Header) - 1244 caracteres.
 * Posicoes conforme ESTRUTURA_DBK_IRPF.md
 */
final class GeradorHeader extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::HEADER;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroHeaderDTO) {
            throw new \InvalidArgumentException('Esperado RegistroHeaderDTO');
        }

        $r = $registro;
        $l = RegistroHeaderDTO::obterLayout();
        $ehRetificadora = $r->tipoDeclaracao === TipoDeclaracao::RETIFICADORA;

        return $l->montarLinha([
            'tipoRegistro' => $this->texto('IRPF', $l->campo('tipoRegistro')->tamanho),
            'anoExercicio' => $this->numero($r->anoExercicio, $l->campo('anoExercicio')->tamanho),
            'anoCalendario' => $this->numero($r->anoCalendario, $l->campo('anoCalendario')->tamanho),
            'codigoVersao' => $this->numero($r->codigoVersao, $l->campo('codigoVersao')->tamanho),
            'tipoModalidadeHeader' => $this->numero($r->tipoModalidadeHeader, $l->campo('tipoModalidadeHeader')->tamanho),
            'tipoDeclaracao' => $r->tipoModalidadeHeader === '20' ? ' ' : $r->tipoDeclaracao->value,
            'cpf' => $r->cpf->valor,
            'codigoNaturezaOcupacao' => $this->numero($r->codigoNaturezaOcupacao, $l->campo('codigoNaturezaOcupacao')->tamanho),
            'nome' => $this->texto($r->nome, $l->campo('nome')->tamanho),
            'uf' => $r->uf !== null ? $r->uf->value : $this->espacos(2),
            'hashValorCalculado' => $this->numero($r->hashValorCalculado, $l->campo('hashValorCalculado')->tamanho),
            'dataNascimento' => $this->data($r->dataNascimento),
            'estadoCivil' => $r->estadoCivil->value,
            'tipoDeclaracaoNumero' => $r->tipoDeclaracaoNumero,
            'flagSN' => $r->flagSN,
            'reciboRetificadora' => $ehRetificadora
                ? $this->numero($r->reciboDeclaracaoAnterior, $l->campo('reciboRetificadora')->tamanho)
                : $this->espacos($l->campo('reciboRetificadora')->tamanho),
            'sistemaOperacional' => $this->texto($r->sistemaOperacional, $l->campo('sistemaOperacional')->tamanho),
            'versaoSO' => $this->texto($r->versaoSO, $l->campo('versaoSO')->tamanho),
            'versaoProgramaIrpf' => $this->texto($r->versaoProgramaIrpf, $l->campo('versaoProgramaIrpf')->tamanho),
            'codigoMunicipioIbge' => $r->codigoMunicipioIbge !== '0000'
                ? $this->numero($r->codigoMunicipioIbge, $l->campo('codigoMunicipioIbge')->tamanho)
                : $this->espacos($l->campo('codigoMunicipioIbge')->tamanho),
            'cpfConjuge' => $this->cpfOuEspacos($r->cpfConjuge, $l->campo('cpfConjuge')->tamanho),
            'reciboNumeroControle' => $this->numero($r->reciboNumeroControle, $l->campo('reciboNumeroControle')->tamanho),
            'reciboOriginal' => !$ehRetificadora && $r->reciboDeclaracaoAnterior !== ''
                ? $this->numero($r->reciboDeclaracaoAnterior, $l->campo('reciboOriginal')->tamanho)
                : $this->espacos($l->campo('reciboOriginal')->tamanho),
            'codigoEnderecoMunicipio' => $this->numero($r->codigoEnderecoMunicipio, $l->campo('codigoEnderecoMunicipio')->tamanho),
            'flagIdentificacaoContrib' => $r->flagIdentificacaoContrib,
            'cep' => $this->numero($r->cep, $l->campo('cep')->tamanho),
            'impostoAPagar' => $this->monetario($r->impostoAPagar, $l->campo('impostoAPagar')->tamanho),
            'flagPos254' => $r->flagPos254,
            'cpfRepetido' => $r->tipoModalidadeHeader === '20'
                ? $this->espacos($l->campo('cpfRepetido')->tamanho)
                : $r->cpf->valor,
            'cnpjFontePrincipal' => $this->cnpjOuZeros($r->cnpjFontePrincipal, $l->campo('cnpjFontePrincipal')->tamanho),
            'cpfDependenteConjuge' => $this->cpfOuEspacos($r->cpfDependenteConjuge, $l->campo('cpfDependenteConjuge')->tamanho),
            'dataNascimentoDependente' => $this->dataOuEspacos($r->dataNascimentoDependente, $l->campo('dataNascimentoDependente')->tamanho),
            'cpfMedicoTerceiro' => $this->cpfOuEspacos($r->cpfMedicoTerceiro, $l->campo('cpfMedicoTerceiro')->tamanho),
            'cidade' => $this->texto($r->cidade, $l->campo('cidade')->tamanho),
            'nomeRepetido' => $this->texto($r->nome, $l->campo('nomeRepetido')->tamanho),
            'hashNumeroRecibo' => $this->numero($r->hashNumeroRecibo, $l->campo('hashNumeroRecibo')->tamanho),
            'separadorHash' => ' ',
            'codigoControle' => $this->texto($r->codigoControle, $l->campo('codigoControle')->tamanho),
            'dataSaidaHeader' => $this->numero($r->dataSaidaHeader, $l->campo('dataSaidaHeader')->tamanho),
            'flagProcuradorHeader' => $r->flagProcuradorHeader,
            'cpfProcuradorHeader' => $this->cpfOuEspacos($r->cpfProcuradorHeader, $l->campo('cpfProcuradorHeader')->tamanho),
            'valorPos701' => $this->monetario($r->valorPos701, $l->campo('valorPos701')->tamanho),
            'valoresFinanceirosRaw' => $this->rawOuZeros($r->valoresFinanceirosRaw, $l->campo('valoresFinanceirosRaw')->tamanho),
            'corRaca' => $r->corRaca,
            'flagCpfMedicoRaw' => $this->rawOuEspacos($r->flagCpfMedicoRaw, $l->campo('flagCpfMedicoRaw')->tamanho),
            'reservadoFinalRaw' => $this->rawOuEspacos($r->reservadoFinalRaw, $l->campo('reservadoFinalRaw')->tamanho),
            'dataResidenciaPaisHeader' => $this->numero($r->dataResidenciaPaisHeader, $l->campo('dataResidenciaPaisHeader')->tamanho),
            'reservadoFinal2Raw' => $this->rawOuEspacos($r->reservadoFinal2Raw, $l->campo('reservadoFinal2Raw')->tamanho),
        ]);
    }
}
