<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;

final class LeitorDependente extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::DEPENDENTE;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroDependenteDTO::obterLayout();

        $cpfDepRaw = $l->extrair($linha, 'cpfDependente');
        $cpfDep = trim($cpfDepRaw) !== '' && $cpfDepRaw !== '00000000000'
            ? new Cpf($cpfDepRaw) : null;

        return new RegistroDependenteDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            sequencial: $l->extrairNumero($linha, 'sequencial'),
            tipoDependente: new CodigoDependente($l->extrair($linha, 'tipoDependente')),
            nomeDependente: $l->extrairTexto($linha, 'nomeDependente'),
            dataNascimento: new Data($l->extrair($linha, 'dataNascimento')),
            cpfDependente: $cpfDep,
            camposAdicionaisRaw: $l->extrair($linha, 'desconhecido'),
            moraTitular: $l->extrair($linha, 'moraTitular') === '1',
            email: $l->extrairTexto($linha, 'email'),
            ddd: trim($l->extrair($linha, 'ddd')),
            celular: trim($l->extrair($linha, 'celular')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
