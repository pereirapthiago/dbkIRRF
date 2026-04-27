<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorBemDireito extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::BEM_DIREITO;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroBemDireitoDTO::obterLayout();

        return new RegistroBemDireitoDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            codigoItem: $l->extrair($linha, 'codigoItem'),
            flagExterior: $l->extrair($linha, 'flagExterior'),
            pais: $l->extrair($linha, 'pais'),
            descricao: $l->extrairTexto($linha, 'descricao'),
            valorAnterior: ValorMonetario::deString($l->extrair($linha, 'valorAnterior')),
            valorAtual: ValorMonetario::deString($l->extrair($linha, 'valorAtual')),
            logradouro: $l->extrairTexto($linha, 'logradouro'),
            numero: $l->extrairTexto($linha, 'numero'),
            complemento: $l->extrairTexto($linha, 'complemento'),
            bairro: $l->extrairTexto($linha, 'bairro'),
            cep: $l->extrair($linha, 'cep'),
            uf: $l->extrairTexto($linha, 'uf'),
            codigoMunicipioIbge: $l->extrair($linha, 'codigoMunicipioIbge'),
            municipio: $l->extrairTexto($linha, 'municipio'),
            camposAdicionaisRaw1: $l->extrair($linha, 'camposAdicionaisRaw1'),
            agencia: $l->extrair($linha, 'agencia'),
            reservado867: $l->extrair($linha, 'reservado867'),
            dvConta: $l->extrair($linha, 'dvConta'),
            camposAdicionaisRaw2: $l->extrair($linha, 'camposAdicionaisRaw2'),
            dataAquisicao: new Data($l->extrair($linha, 'dataAquisicao')),
            reservado905: $l->extrair($linha, 'reservado905'),
            renavam: $l->extrair($linha, 'renavam'),
            numeroConta: $l->extrair($linha, 'numeroConta'),
            camposAdicionaisRaw3: $l->extrair($linha, 'camposAdicionaisRaw3'),
            aplicFinancRendPerda: ValorMonetario::deString($l->extrair($linha, 'aplicFinancRendPerda')),
            aplicFinancImpExterior: ValorMonetario::deString($l->extrair($linha, 'aplicFinancImpExterior')),
            cnpj: $l->extrairTexto($linha, 'cnpj'),
            camposAdicionaisRaw4: $l->extrair($linha, 'camposAdicionaisRaw4'),
            codigoGrupo: $l->extrair($linha, 'codigoGrupo'),
            camposAdicionaisRaw5a: $l->extrair($linha, 'camposAdicionaisRaw5a'),
            aplicFinancRendPerdaAlt: ValorMonetario::deString($l->extrair($linha, 'aplicFinancRendPerdaAlt')),
            aplicFinancImpExteriorAlt: ValorMonetario::deString($l->extrair($linha, 'aplicFinancImpExteriorAlt')),
            lucrosDivValorRecebido: ValorMonetario::deString($l->extrair($linha, 'lucrosDivValorRecebido')),
            lucrosDivImpostoPago: ValorMonetario::deString($l->extrair($linha, 'lucrosDivImpostoPago')),
            camposAdicionaisRaw5b: $l->extrair($linha, 'camposAdicionaisRaw5b'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
