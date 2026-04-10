<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroSaidaDefinitivaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;

final class LeitorSaidaDefinitiva extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::SAIDA_DEFINITIVA;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroSaidaDefinitivaDTO::obterLayout();

        return new RegistroSaidaDefinitivaDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            cpfProcurador: ($raw = $l->extrair($linha, 'cpfProcurador')) && trim($raw) !== ''
                ? new Cpf($raw) : null,
            nomeProcurador: $l->extrairTexto($linha, 'nomeProcurador'),
            enderecoProcurador: $l->extrairTexto($linha, 'enderecoProcurador'),
            dataNaoResidente: new Data($l->extrair($linha, 'dataNaoResidente')),
            dataResidentePais: new Data($l->extrair($linha, 'dataResidentePais')),
            codigoPaisDestino: ltrim($l->extrair($linha, 'codigoPaisDestino'), '0') ?: '0',
            checksum: Checksum::deLinha($linha),
        );
    }
}
