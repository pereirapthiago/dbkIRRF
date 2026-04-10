<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Formatador;

use DbkIrrf\Infraestrutura\Formatador\FormatadorNumerico;
use PHPUnit\Framework\TestCase;

final class FormatadorNumericoTest extends TestCase
{
    private FormatadorNumerico $formatador;

    protected function setUp(): void
    {
        $this->formatador = new FormatadorNumerico();
    }

    public function testDevePreencherComZerosAEsquerda(): void
    {
        $this->assertSame('0042', $this->formatador->formatar('42', 4));
    }

    public function testDeveRemoverNaoNumericos(): void
    {
        $this->assertSame('00041653508000', $this->formatador->formatar('416.535.080-00', 14));
    }

    public function testDeveFormatarInteiro(): void
    {
        $this->assertSame('00028', $this->formatador->formatarInteiro(28, 5));
        $this->assertSame('2026', $this->formatador->formatarInteiro(2026, 4));
    }

    public function testDeveFormatarZero(): void
    {
        $this->assertSame('0000', $this->formatador->formatar('0', 4));
        $this->assertSame('0000', $this->formatador->formatarInteiro(0, 4));
    }
}
