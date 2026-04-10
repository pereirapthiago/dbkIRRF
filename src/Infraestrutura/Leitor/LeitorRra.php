<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorRra extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RRA;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroRraDTO::obterLayout();

        return new RegistroRraDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            cnpjFontePagadora: new Cnpj($l->extrair($linha, 'cnpjFontePagadora')),
            nomeFontePagadora: $l->extrairTexto($linha, 'nomeFontePagadora'),
            contribPrevidenciaria: ValorMonetario::deString($l->extrair($linha, 'contribPrevidenciaria')),
            parcelaIsenta65Anos: ValorMonetario::deString($l->extrair($linha, 'parcelaIsenta65Anos')),
            impostoRetidoFonte: ValorMonetario::deString($l->extrair($linha, 'impostoRetidoFonte')),
            mesRecebimentoRRA: $l->extrair($linha, 'mesRecebimentoRRA'),
            metadadosRRA: $l->extrair($linha, 'metadadosRRA'),
            flagCodigo150: $l->extrair($linha, 'flagCodigo150'),
            numMesesRRA: $l->extrair($linha, 'numMesesRRA'),
            impostoBrutoRRA: ValorMonetario::deString($l->extrair($linha, 'impostoBrutoRRA')),
            rendimentosRRA: ValorMonetario::deString($l->extrair($linha, 'rendimentosRRA')),
            rendimentosRRACopia: ValorMonetario::deString($l->extrair($linha, 'rendimentosRRACopia')),
            campoDesconhecido194: ValorMonetario::deString($l->extrair($linha, 'campoDesconhecido194')),
            reservadoPos154: $l->extrair($linha, 'reservadoPos154'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
