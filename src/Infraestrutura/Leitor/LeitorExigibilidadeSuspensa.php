<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroExigibilidadeSuspensaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorExigibilidadeSuspensa extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::EXIGIBILIDADE_SUSPENSA;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroExigibilidadeSuspensaDTO::obterLayout();

        return new RegistroExigibilidadeSuspensaDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            cnpjFontePagadora: new Cnpj($l->extrair($linha, 'cnpjFontePagadora')),
            nomeFontePagadora: $l->extrairTexto($linha, 'nomeFontePagadora'),
            rendimentosTributaveis: ValorMonetario::deCentavos((int) $l->extrair($linha, 'rendimentosTributaveis')),
            depositosJudiciais: ValorMonetario::deCentavos((int) $l->extrair($linha, 'depositosJudiciais')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
