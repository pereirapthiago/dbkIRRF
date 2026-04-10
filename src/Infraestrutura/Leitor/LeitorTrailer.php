<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;

/**
 * Leitor do registro T9 - Trailer/Totalizador - 449 caracteres.
 * Posicoes 1-based conforme ESTRUTURA_DBK_IRPF.md
 */
final class LeitorTrailer extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::TRAILER;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroTrailerDTO::obterLayout();

        return new RegistroTrailerDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            totalRegistros: $l->extrairNumero($linha, 'totalRegistros'),
            contadoresRaw: $l->extrair($linha, 'contadoresRaw'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
