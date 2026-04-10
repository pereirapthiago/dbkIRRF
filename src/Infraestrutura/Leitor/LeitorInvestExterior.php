<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorInvestExterior extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::INVESTIMENTO_EXTERIOR;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroInvestExteriorDTO::obterLayout();

        return new RegistroInvestExteriorDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            idBem: $l->extrair($linha, 'idBem'),
            sequencialDetalhe: $l->extrair($linha, 'sequencialDetalhe'),
            subTipo: SubTipoInvestimento::from($l->extrair($linha, 'subTipo')),
            rendimentoValor: ValorMonetario::deString($l->extrair($linha, 'rendimentoValor')),
            impostoDevido15: ValorMonetario::deString($l->extrair($linha, 'impostoDevido15')),
            impostoPagoExterior: ValorMonetario::deString($l->extrair($linha, 'impostoPagoExterior')),
            campoMonetario4: ValorMonetario::deString($l->extrair($linha, 'campoMonetario4')),
            campoMonetario5: ValorMonetario::deString($l->extrair($linha, 'campoMonetario5')),
            grupoBem: $l->extrair($linha, 'grupoBem'),
            codigoItem: $l->extrair($linha, 'codigoItem'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
