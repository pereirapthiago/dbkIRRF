<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorTribExclusiva extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::TRIBUTACAO_EXCLUSIVA;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroTribExclusivaDTO::obterLayout();

        return new RegistroTribExclusivaDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            tipoBeneficiario: TipoBeneficiario::from($l->extrair($linha, 'tipoBeneficiario')),
            cpfBeneficiario: new Cpf($l->extrair($linha, 'cpfBeneficiario')),
            codigoTipoRendimento: $l->extrair($linha, 'codigoTipoRendimento'),
            cnpjFontePagadora: new Cnpj($l->extrair($linha, 'cnpjFontePagadora')),
            nomeFontePagadora: $l->extrairTexto($linha, 'nomeFontePagadora'),
            valorRendimento: ValorMonetario::deString($l->extrair($linha, 'valorRendimento')),
            checksum: Checksum::deLinha($linha),
        );
    }
}
