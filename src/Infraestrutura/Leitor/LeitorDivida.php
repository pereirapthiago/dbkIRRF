<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorDivida extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DIVIDA;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroDividaDTO::obterLayout();

        return new RegistroDividaDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            codigoDivida: $l->extrair($linha, 'codigoDivida'),
            descricao: $l->extrairTexto($linha, 'descricao'),
            saldoAnterior: ValorMonetario::deString($l->extrair($linha, 'saldoAnterior')),
            saldoAtual: ValorMonetario::deString($l->extrair($linha, 'saldoAtual')),
            valorPagoAno: ValorMonetario::deString($l->extrair($linha, 'valorPagoAno')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
