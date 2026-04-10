<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Leitor do registro 22 - Rendimentos Mensais PF/Exterior - 167 caracteres.
 */
final class LeitorRendimentosMensais extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTOS_MENSAIS;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroRendimentosMensaisDTO::obterLayout();

        return new RegistroRendimentosMensaisDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            mesReferencia: $l->extrairNumero($linha, 'mesReferencia'),
            rendNaoAssalariado: ValorMonetario::deString($l->extrair($linha, 'rendNaoAssalariado')),
            temporada: ValorMonetario::deString($l->extrair($linha, 'temporada')),
            outrosRendimentos: ValorMonetario::deString($l->extrair($linha, 'outrosRendimentos')),
            exterior: ValorMonetario::deString($l->extrair($linha, 'exterior')),
            previdencia: ValorMonetario::deString($l->extrair($linha, 'previdencia')),
            dependentes: ValorMonetario::deString($l->extrair($linha, 'dependentes')),
            pensaoAlimenticia: ValorMonetario::deString($l->extrair($linha, 'pensaoAlimenticia')),
            livroCaixa: ValorMonetario::deString($l->extrair($linha, 'livroCaixa')),
            totalRendimentosMes: ValorMonetario::deString($l->extrair($linha, 'totalRendimentosMes')),
            darfPago: ValorMonetario::deString($l->extrair($linha, 'darfPago')),
            flagNS: $l->extrair($linha, 'flagNS'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
