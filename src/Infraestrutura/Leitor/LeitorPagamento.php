<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

final class LeitorPagamento extends LeitorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::PAGAMENTO;
    }

    protected function lerCampos(string $linha): RegistroInterface
    {
        $l = RegistroPagamentoDTO::obterLayout();

        return new RegistroPagamentoDTO(
            cpf: new Cpf($l->extrair($linha, 'cpf')),
            codigoPagamento: $l->extrair($linha, 'codigoPagamento'),
            cpfCnpjBeneficiario: $l->extrairTexto($linha, 'cpfCnpjBeneficiario'),
            nomeBeneficiario: $l->extrairTexto($linha, 'nomeBeneficiario'),
            valorPago: ValorMonetario::deString($l->extrair($linha, 'valorPago')),
            parcelaNaoDedutivel: ValorMonetario::deString($l->extrair($linha, 'parcelaNaoDedutivel')),
            sequencial: $l->extrair($linha, 'sequencial'),
            flagTitularDependente: $l->extrair($linha, 'flagTitularDependente'),
            descricao: $l->extrairTexto($linha, 'descricao'),
            codigoPais: $l->extrair($linha, 'codigoPais'),
            checksum: Checksum::deLinha($linha),
        );
    }
}
