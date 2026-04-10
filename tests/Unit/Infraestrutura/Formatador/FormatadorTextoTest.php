<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Formatador;

use DbkIrrf\Infraestrutura\Formatador\FormatadorTexto;
use PHPUnit\Framework\TestCase;

final class FormatadorTextoTest extends TestCase
{
    private FormatadorTexto $formatador;

    protected function setUp(): void
    {
        $this->formatador = new FormatadorTexto();
    }

    public function testDevePreencherComEspacosADireita(): void
    {
        $resultado = $this->formatador->formatar('JORGE', 60);

        $this->assertSame(60, strlen($resultado));
        $this->assertSame('JORGE', rtrim($resultado));
    }

    public function testDeveConverterParaMaiusculas(): void
    {
        $resultado = $this->formatador->formatar('jorge lucas', 20);

        $this->assertStringStartsWith('JORGE LUCAS', $resultado);
    }

    public function testDeveRemoverAcentos(): void
    {
        $resultado = $this->formatador->formatar('Petrópolis', 20);

        $this->assertStringStartsWith('PETROPOLIS', $resultado);
    }

    public function testDeveTruncarTextoExcedente(): void
    {
        $resultado = $this->formatador->formatar('TEXTO MUITO GRANDE', 10);

        $this->assertSame(10, strlen($resultado));
        $this->assertSame('TEXTO MUIT', $resultado);
    }

    public function testDeveRetornarEspacosParaStringVazia(): void
    {
        $resultado = $this->formatador->formatar('', 5);

        $this->assertSame('     ', $resultado);
        $this->assertSame(5, strlen($resultado));
    }

    public function testDeveRemoverCedilha(): void
    {
        $resultado = $this->formatador->formatar('comunicação', 15);

        $this->assertStringStartsWith('COMUNICACAO', $resultado);
    }
}
