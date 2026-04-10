<?php

declare(strict_types=1);

namespace DbkIrrf\Aplicacao\Fabrica;

use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\ValorObjeto\Cpf;

final class FabricaDeclaracao
{
    public function criar(
        Cpf $cpf,
        int $anoExercicio = 2026,
        int $anoCalendario = 2025,
        TipoDeclaracao $tipoDeclaracao = TipoDeclaracao::ORIGINAL,
    ): DeclaracaoDTO {
        $declaracao = new DeclaracaoDTO();

        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            anoExercicio: $anoExercicio,
            anoCalendario: $anoCalendario,
            tipoDeclaracao: $tipoDeclaracao,
        );

        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: $cpf,
            tipoDeclaracao: $tipoDeclaracao,
        );

        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        return $declaracao;
    }
}
