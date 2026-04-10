<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento84DTO;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentoIsento84;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentoIsento84;
use PHPUnit\Framework\TestCase;

final class LeitorRendimentoIsento84Test extends TestCase
{
    private GeradorRendimentoIsento84 $gerador;
    private LeitorRendimentoIsento84 $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorRendimentoIsento84();
        $this->leitor = new LeitorRendimentoIsento84();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroRendimentoIsento84DTO(
            cpf: new Cpf('12345678901'),
            tipoBeneficiario: TipoBeneficiario::TITULAR,
            cpfBeneficiario: new Cpf('12345678901'),
            codigoTipoRendimento: '0001',
            cnpjFontePagadora: new Cnpj('12345678000199'),
            nomeFontePagadora: 'EMPRESA PAGADORA LTDA',
            valorRendimentoIsento: new ValorMonetario(150000),
            valorAdicional: new ValorMonetario(25000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(144, strlen($linha));

        /** @var RegistroRendimentoIsento84DTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame(TipoBeneficiario::TITULAR, $lido->tipoBeneficiario);
        $this->assertSame('12345678901', $lido->cpfBeneficiario->valor);
        $this->assertSame('0001', $lido->codigoTipoRendimento);
        $this->assertSame('12345678000199', $lido->cnpjFontePagadora->valor);
        $this->assertSame('EMPRESA PAGADORA LTDA', $lido->nomeFontePagadora);
        $this->assertSame(150000, $lido->valorRendimentoIsento->centavos);
        $this->assertSame(25000, $lido->valorAdicional->centavos);
    }

    public function testDeveSuportarTipoRendimentoIsento(): void
    {
        $this->assertSame(TipoRegistro::RENDIMENTO_ISENTO, $this->leitor->suportaTipo());
    }
}
