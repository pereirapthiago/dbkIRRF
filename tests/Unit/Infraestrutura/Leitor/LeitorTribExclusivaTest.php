<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorTribExclusiva;
use DbkIrrf\Infraestrutura\Leitor\LeitorTribExclusiva;
use PHPUnit\Framework\TestCase;

final class LeitorTribExclusivaTest extends TestCase
{
    private GeradorTribExclusiva $gerador;
    private LeitorTribExclusiva $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorTribExclusiva();
        $this->leitor = new LeitorTribExclusiva();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroTribExclusivaDTO(
            cpf: new Cpf('12345678901'),
            tipoBeneficiario: TipoBeneficiario::TITULAR,
            cpfBeneficiario: new Cpf('12345678901'),
            codigoTipoRendimento: '0006',
            cnpjFontePagadora: new Cnpj('12345678000199'),
            nomeFontePagadora: 'CORRETORA INVESTIMENTOS SA',
            valorRendimento: new ValorMonetario(150000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(131, strlen($linha));

        /** @var RegistroTribExclusivaDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame(TipoBeneficiario::TITULAR, $lido->tipoBeneficiario);
        $this->assertSame('12345678901', $lido->cpfBeneficiario->valor);
        $this->assertSame('0006', $lido->codigoTipoRendimento);
        $this->assertSame('12345678000199', $lido->cnpjFontePagadora->valor);
        $this->assertSame('CORRETORA INVESTIMENTOS SA', $lido->nomeFontePagadora);
        $this->assertSame(150000, $lido->valorRendimento->centavos);
    }

    public function testDeveSuportarTipoTributacaoExclusiva(): void
    {
        $this->assertSame(TipoRegistro::TRIBUTACAO_EXCLUSIVA, $this->leitor->suportaTipo());
    }
}
