<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\Enum\FlagSimNao;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;

/**
 * Leitor do registro 16 - Dados Pessoais - 930 caracteres.
 * Posicoes 1-based conforme ESTRUTURA_DBK_IRPF.md
 */
final class LeitorDadosPessoais extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DADOS_PESSOAIS;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroDadosPessoaisDTO::obterLayout();

        $flagRetif = $l->extrair($linha, 'flagRetificadora');
        $tipoDeclaracao = $flagRetif === 'S'
            ? TipoDeclaracao::RETIFICADORA
            : TipoDeclaracao::ORIGINAL;

        $ehRetificadora = $tipoDeclaracao === TipoDeclaracao::RETIFICADORA;
        $reciboAnterior = $ehRetificadora
            ? trim($l->extrair($linha, 'reciboRetificadora'))
            : trim($l->extrair($linha, 'reciboOriginal'));

        $cpfConjugeRaw = $l->extrair($linha, 'cpfConjuge');
        $cpfConjuge = trim($cpfConjugeRaw) !== '' && $cpfConjugeRaw !== '00000000000'
            ? new Cpf($cpfConjugeRaw) : null;

        $flagResidPais = $l->extrair($linha, 'flagResidenciaPais');
        $dataResidPais = new Data($l->extrair($linha, 'dataResidenciaPais'));

        return new RegistroDadosPessoaisDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            nome: $l->extrairTexto($linha, 'nome'),
            tipoLogradouro: $l->extrairTexto($linha, 'tipoLogradouro'),
            logradouro: $l->extrairTexto($linha, 'logradouro'),
            numero: $l->extrairTexto($linha, 'numero'),
            complemento: $l->extrairTexto($linha, 'complemento'),
            bairro: $l->extrairTexto($linha, 'bairro'),
            cep: $l->extrair($linha, 'cep'),
            codigoMunicipioIbge: $l->extrair($linha, 'codigoMunicipioIbge'),
            municipio: $l->extrairTexto($linha, 'municipio'),
            uf: trim($l->extrair($linha, 'uf')) !== '' ? UnidadeFederativa::from($l->extrair($linha, 'uf')) : null,
            email: $l->extrairTexto($linha, 'email'),
            dddCelular: $l->extrairTexto($linha, 'dddCelular'),
            celular: $l->extrairTexto($linha, 'celular'),
            dddFixo: $l->extrairTexto($linha, 'dddFixo'),
            telefoneFixo: $l->extrairTexto($linha, 'telefoneFixo'),
            dataNascimento: new Data($l->extrair($linha, 'dataNascimento')),
            codigoOcupacao: $l->extrair($linha, 'codigoOcupacao'),
            cpfConjuge: $cpfConjuge,
            tipoDeclaracao: $tipoDeclaracao,
            flagAlteracaoCadastral: FlagSimNao::tryFrom($l->extrair($linha, 'flagAlteracaoCadastral')) ?? FlagSimNao::NAO,
            reciboDeclaracaoAnterior: $reciboAnterior,
            flagResidenciaPais: $flagResidPais,
            dataResidenciaPais: $dataResidPais,
            reservadoCodigo: $l->extrair($linha, 'reservadoCodigo'),
            flagPos381: $l->extrair($linha, 'flagPos381'),
            naturezaOcupacao: $l->extrair($linha, 'naturezaOcupacao'),
            flagSN387: $l->extrair($linha, 'flagSN387'),
            flagNS388: $l->extrair($linha, 'flagNS388'),
            flagNS390: $l->extrair($linha, 'flagNS390'),
            flagA454: $l->extrair($linha, 'flagA454'),
            flagPossuiConjuge: FlagSimNao::tryFrom($l->extrair($linha, 'flagPossuiConjuge')) ?? FlagSimNao::NAO,
            camposAdicionaisRaw: $l->extrair($linha, 'camposAdicionaisRaw'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
