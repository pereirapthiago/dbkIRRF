<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Leitor do registro 23 - Imposto Pago/Retido - 40 caracteres.
 */
final class LeitorImpostoPago extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::IMPOSTO_PAGO;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroImpostoPagoDTO::obterLayout();

        return new RegistroImpostoPagoDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            codigo: $l->extrair($linha, 'codigo'),
            valor: ValorMonetario::deString($l->extrair($linha, 'valor')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
