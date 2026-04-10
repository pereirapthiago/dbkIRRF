<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\ValorObjeto;

use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use PHPUnit\Framework\TestCase;

/**
 * Bug Hunter - Fase 5: Edge cases e cenarios extremos para Value Objects.
 */
final class EdgeCasesTest extends TestCase
{
    // ========== CPF ==========

    public function testCpfVazioDeveLancarExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cpf('');
    }

    public function testCpfComLetrasDeveLancarExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cpf('ABCDEFGHIJK');
    }

    public function testCpfSoComPontosDeveLancarExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cpf('...-');
    }

    public function testCpfTodosZerosDeveSerValido(): void
    {
        $cpf = new Cpf('00000000000');
        $this->assertSame('00000000000', $cpf->valor);
    }

    // ========== CNPJ ==========

    public function testCnpjVazioDeveLancarExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cnpj('');
    }

    // ========== ValorMonetario ==========

    public function testValorMonetarioZeroDeveFormatarCorretamente(): void
    {
        $v = ValorMonetario::zero();
        $this->assertSame('0000000000000', $v->formatar());
        $this->assertSame(0.00, $v->emReais());
    }

    public function testValorMonetarioMaximo13Digitos(): void
    {
        // Maximo possivel em 13 digitos: 9999999999999 centavos = R$ 99.999.999.999,99
        $v = ValorMonetario::deCentavos(9999999999999);
        $this->assertSame('9999999999999', $v->formatar());
    }

    public function testValorMonetarioDeStringVazia(): void
    {
        $v = ValorMonetario::deString('0000000000000');
        $this->assertSame(0, $v->centavos);
    }

    public function testValorMonetarioDeStringComUmCentavo(): void
    {
        $v = ValorMonetario::deString('0000000000001');
        $this->assertSame(1, $v->centavos);
        $this->assertSame(0.01, $v->emReais());
    }

    public function testValorMonetarioNegativoDeveLancarExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ValorMonetario(-1);
    }

    public function testValorMonetarioDeReaisComArredondamento(): void
    {
        $v = ValorMonetario::deReais(250756.28);
        $this->assertSame(25075628, $v->centavos);
        $this->assertSame('0000025075628', $v->formatar());
    }

    // ========== Data ==========

    public function testDataVaziaDeveSerReconhecida(): void
    {
        $this->assertTrue(Data::vazia()->eVazia());
        $this->assertTrue(Data::espacosVazios()->eVazia());
    }

    public function testDataComTamanhoErrado(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Data('1234');
    }

    public function testDataComLetrasDiferentes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Data('ABCDEFGH');
    }

    public function testDataDeDateTime31Dez(): void
    {
        $d = Data::deDateTime(new \DateTime('2025-12-31'));
        $this->assertSame('31122025', $d->valor);
        $this->assertSame(31, $d->obterDia());
        $this->assertSame(12, $d->obterMes());
        $this->assertSame(2025, $d->obterAno());
    }

    public function testDataDeDateTime01Jan(): void
    {
        $d = Data::deDateTime(new \DateTime('2025-01-01'));
        $this->assertSame('01012025', $d->valor);
    }

    // ========== Checksum ==========

    public function testChecksumComTamanhoErrado(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Checksum('12345');
    }

    public function testChecksumDeLinhaComExatamente10Chars(): void
    {
        $cs = Checksum::deLinha('1234567890');
        $this->assertSame('1234567890', $cs->valor);
    }
}
