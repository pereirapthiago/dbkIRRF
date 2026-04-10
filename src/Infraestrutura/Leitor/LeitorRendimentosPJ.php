<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Leitor do registro 21 - Rendimentos Tributaveis PJ - 170 caracteres.
 */
final class LeitorRendimentosPJ extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTOS_PJ;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroRendimentosPJDTO::obterLayout();

        return new RegistroRendimentosPJDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            cnpjFontePagadora: new Cnpj($l->extrair($linha, 'cnpjFontePagadora')),
            nomeFontePagadora: $l->extrairTexto($linha, 'nomeFontePagadora'),
            rendimentosRecebidos: ValorMonetario::deString($l->extrair($linha, 'rendimentosRecebidos')),
            contribPrevidenciaria: ValorMonetario::deString($l->extrair($linha, 'contribPrevidenciaria')),
            decimoTerceiroSalario: ValorMonetario::deString($l->extrair($linha, 'decimoTerceiroSalario')),
            impostoRetidoFonte: ValorMonetario::deString($l->extrair($linha, 'impostoRetidoFonte')),
            irrfDecimoTerceiro: ValorMonetario::deString($l->extrair($linha, 'irrfDecimoTerceiro')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
