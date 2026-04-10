<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Formatador;

use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Formatador\FormatadorMonetario;
use PHPUnit\Framework\TestCase;

final class FormatadorMonetarioTest extends TestCase
{
    private FormatadorMonetario $formatador;

    protected function setUp(): void
    {
        $this->formatador = new FormatadorMonetario();
    }

    public function testDeveFormatarValorMonetarioCom13Digitos(): void
    {
        $valor = ValorMonetario::deCentavos(18000000);
        $this->assertSame('0000018000000', $this->formatador->formatarValor($valor));
    }

    public function testDeveFormatarCentavosQuebrados(): void
    {
        $this->assertSame('0000025075628', $this->formatador->formatarCentavos(25075628));
    }

    public function testDeveFormatarZero(): void
    {
        $this->assertSame('0000000000000', $this->formatador->formatarValor(ValorMonetario::zero()));
    }

    public function testDeveFormatarComTamanhoCustomizado(): void
    {
        $valor = ValorMonetario::deCentavos(1480109);
        $this->assertSame('1480109', $this->formatador->formatarValor($valor, 7));
    }

    public function testDeveFormatarStringViaInterface(): void
    {
        $this->assertSame('0000018000000', $this->formatador->formatar('18000000', 13));
    }
}
