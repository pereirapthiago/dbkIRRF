<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\ValorObjeto;

use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use PHPUnit\Framework\TestCase;

final class ValorMonetarioTest extends TestCase
{
    public function testDeveCriarDeReais(): void
    {
        $valor = ValorMonetario::deReais(180000.00);

        $this->assertSame(18000000, $valor->centavos);
        $this->assertSame(180000.00, $valor->emReais());
    }

    public function testDeveFormatarCom13Digitos(): void
    {
        $valor = ValorMonetario::deCentavos(18000000);

        $this->assertSame('0000018000000', $valor->formatar());
        $this->assertSame('0000018000000', (string) $valor);
    }

    public function testDeveFormatarZero(): void
    {
        $valor = ValorMonetario::zero();

        $this->assertSame('0000000000000', $valor->formatar());
        $this->assertSame(0, $valor->centavos);
    }

    public function testDeveInterpretarStringComZerosAEsquerda(): void
    {
        $valor = ValorMonetario::deString('0000018000000');

        $this->assertSame(18000000, $valor->centavos);
        $this->assertSame(180000.00, $valor->emReais());
    }

    public function testDeveInterpretarCentavosQuebrados(): void
    {
        // R$ 250.756,28 = 25075628 centavos
        $valor = ValorMonetario::deString('0000025075628');

        $this->assertSame(25075628, $valor->centavos);
        $this->assertSame(250756.28, $valor->emReais());
    }

    public function testDeveLancarExcecaoParaValorNegativo(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ValorMonetario::deCentavos(-100);
    }

    public function testDeveFormatarComTamanhoCustomizado(): void
    {
        $valor = ValorMonetario::deCentavos(1480109);

        $this->assertSame('1480109', $valor->formatar(7));
    }

    public function testDeveCompararIgualdade(): void
    {
        $v1 = ValorMonetario::deCentavos(18000000);
        $v2 = ValorMonetario::deReais(180000.00);
        $v3 = ValorMonetario::deCentavos(12000000);

        $this->assertTrue($v1->igual($v2));
        $this->assertFalse($v1->igual($v3));
    }
}
