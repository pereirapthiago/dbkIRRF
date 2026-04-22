<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento86DTO;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorRendimentoIsento86 extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTO_ISENTO_OUTROS;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroRendimentoIsento86DTO::obterLayout();

        return new RegistroRendimentoIsento86DTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            tipoBeneficiario: TipoBeneficiario::from($l->extrair($linha, 'tipoBeneficiario')),
            cpfBeneficiario: new Cpf($l->extrair($linha, 'cpfBeneficiario')),
            codigoTipoRendimento: $l->extrair($linha, 'codigoTipoRendimento'),
            cnpjFontePagadora: new Cnpj($l->extrair($linha, 'cnpjFontePagadora')),
            nomeFontePagadora: $l->extrairTexto($linha, 'nomeFontePagadora'),
            valorRendimentoIsento: ValorMonetario::deString($l->extrair($linha, 'valorRendimentoIsento')),
            descricaoLivre: $l->extrairTexto($linha, 'descricaoLivre'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
