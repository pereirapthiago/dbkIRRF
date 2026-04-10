<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Leitor do registro 24 - Deducoes Legais - 40 caracteres.
 */
final class LeitorDeducaoLegal extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DEDUCAO_LEGAL;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroDeducaoLegalDTO::obterLayout();

        return new RegistroDeducaoLegalDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            codigoDeducao: $l->extrair($linha, 'codigoDeducao'),
            valor: ValorMonetario::deString($l->extrair($linha, 'valor')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
