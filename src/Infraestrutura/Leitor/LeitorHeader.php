<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Leitor do registro IRPF (Header) - 1244 caracteres.
 * Posicoes 1-based conforme ESTRUTURA_DBK_IRPF.md
 */
final class LeitorHeader extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::HEADER;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroHeaderDTO::obterLayout();

        $tipoDeclaracaoRaw = $l->extrair($linha, 'tipoDeclaracao');
        $tipoDeclaracao = trim($tipoDeclaracaoRaw) === ''
            ? TipoDeclaracao::ORIGINAL
            : TipoDeclaracao::from($tipoDeclaracaoRaw);
        $ehRetificadora = $tipoDeclaracao === TipoDeclaracao::RETIFICADORA;

        $reciboAnterior = $ehRetificadora
            ? trim($l->extrair($linha, 'reciboRetificadora'))
            : trim($l->extrair($linha, 'reciboOriginal'));

        $cnpjRaw = $l->extrair($linha, 'cnpjFontePrincipal');
        $cnpjFonte = trim($cnpjRaw) !== '' && $cnpjRaw !== '00000000000000'
            ? new Cnpj($cnpjRaw) : null;

        $cpfDepRaw = $l->extrair($linha, 'cpfDependenteConjuge');
        $cpfDep = trim($cpfDepRaw) !== '' && $cpfDepRaw !== '00000000000'
            ? new Cpf($cpfDepRaw) : null;

        $cpfProcRaw = $l->extrair($linha, 'cpfProcuradorHeader');
        $cpfProc = trim($cpfProcRaw) !== '' && $cpfProcRaw !== '00000000000'
            ? new Cpf($cpfProcRaw) : null;

        $cpfConjugeRaw = $l->extrair($linha, 'cpfConjuge');
        $cpfConj = trim($cpfConjugeRaw) !== '' && $cpfConjugeRaw !== '00000000000'
            ? new Cpf($cpfConjugeRaw) : null;

        $cpfMedRaw = $l->extrair($linha, 'cpfMedicoTerceiro');
        $cpfMed = trim($cpfMedRaw) !== '' && $cpfMedRaw !== '00000000000'
            ? new Cpf($cpfMedRaw) : null;

        $dataNascDepRaw = $l->extrair($linha, 'dataNascimentoDependente');
        $dataNascDep = trim($dataNascDepRaw) !== '' && $dataNascDepRaw !== '00000000'
            ? new Data($l->extrair($linha, 'dataNascimentoDependente')) : null;

        return new RegistroHeaderDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            anoExercicio: $l->extrairNumero($linha, 'anoExercicio'),
            anoCalendario: $l->extrairNumero($linha, 'anoCalendario'),
            codigoVersao: $l->extrair($linha, 'codigoVersao'),
            tipoDeclaracao: $tipoDeclaracao,
            codigoNaturezaOcupacao: $l->extrair($linha, 'codigoNaturezaOcupacao'),
            nome: $l->extrairTexto($linha, 'nome'),
            uf: trim($l->extrair($linha, 'uf')) !== '' ? UnidadeFederativa::from($l->extrair($linha, 'uf')) : null,
            dataNascimento: new Data($l->extrair($linha, 'dataNascimento')),
            estadoCivil: EstadoCivil::from($l->extrair($linha, 'estadoCivil')),
            codigoMunicipioIbge: $l->extrair($linha, 'codigoMunicipioIbge'),
            codigoEnderecoMunicipio: $l->extrair($linha, 'codigoEnderecoMunicipio'),
            flagIdentificacaoContrib: $l->extrair($linha, 'flagIdentificacaoContrib'),
            cep: $l->extrair($linha, 'cep'),
            cidade: $l->extrairTexto($linha, 'cidade'),
            reciboDeclaracaoAnterior: $reciboAnterior,
            impostoAPagar: ValorMonetario::deString($l->extrair($linha, 'impostoAPagar')),
            cnpjFontePrincipal: $cnpjFonte,
            cpfDependenteConjuge: $cpfDep,
            dataNascimentoDependente: $dataNascDep,
            cpfMedicoTerceiro: $cpfMed,
            cpfConjuge: $cpfConj,
            sistemaOperacional: $l->extrairTexto($linha, 'sistemaOperacional'),
            versaoSO: $l->extrairTexto($linha, 'versaoSO'),
            versaoProgramaIrpf: $l->extrairTexto($linha, 'versaoProgramaIrpf'),
            hashValorCalculado: $l->extrair($linha, 'hashValorCalculado'),
            tipoDeclaracaoNumero: $l->extrair($linha, 'tipoDeclaracaoNumero'),
            flagSN: $l->extrair($linha, 'flagSN'),
            flagPos254: $l->extrair($linha, 'flagPos254'),
            reciboNumeroControle: $l->extrair($linha, 'reciboNumeroControle'),
            hashNumeroRecibo: $l->extrair($linha, 'hashNumeroRecibo'),
            tipoModalidadeHeader: $l->extrair($linha, 'tipoModalidadeHeader'),
            dataSaidaHeader: $l->extrair($linha, 'dataSaidaHeader'),
            flagProcuradorHeader: $l->extrair($linha, 'flagProcuradorHeader'),
            cpfProcuradorHeader: $cpfProc,
            codigoControle: $l->extrairTexto($linha, 'codigoControle'),
            valorPos701: ValorMonetario::deString($l->extrair($linha, 'valorPos701')),
            corRaca: $l->extrair($linha, 'corRaca'),
            valoresFinanceirosRaw: $l->extrair($linha, 'valoresFinanceirosRaw'),
            flagCpfMedicoRaw: $l->extrair($linha, 'flagCpfMedicoRaw'),
            reservadoFinalRaw: $l->extrair($linha, 'reservadoFinalRaw'),
            dataResidenciaPaisHeader: $l->extrair($linha, 'dataResidenciaPaisHeader'),
            reservadoFinal2Raw: $l->extrair($linha, 'reservadoFinal2Raw'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
