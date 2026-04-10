<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\ValorObjeto;

use DbkIrrf\Dominio\ValorObjeto\Data;
use PHPUnit\Framework\TestCase;

final class DataTest extends TestCase
{
    public function testDeveCriarDataValida(): void
    {
        $data = new Data('10102000');
        $this->assertSame('10102000', $data->valor);
        $this->assertSame(10, $data->obterDia());
        $this->assertSame(10, $data->obterMes());
        $this->assertSame(2000, $data->obterAno());
    }

    public function testDeveCriarDeDateTime(): void
    {
        $data = Data::deDateTime(new \DateTime('2021-03-15'));
        $this->assertSame('15032021', $data->valor);
    }

    public function testDeveRetornarDataVazia(): void
    {
        $data = Data::vazia();
        $this->assertSame('00000000', $data->valor);
        $this->assertTrue($data->eVazia());
    }

    public function testDeveRetornarEspacosVazios(): void
    {
        $data = Data::espacosVazios();
        $this->assertSame('        ', $data->valor);
        $this->assertTrue($data->eVazia());
    }

    public function testDeveLancarExcecaoParaTamanhoIncorreto(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Data('123');
    }

    public function testDeveLancarExcecaoParaCaracteresInvalidos(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Data('ABCDEFGH');
    }

    public function testToStringDeveRetornarValor(): void
    {
        $data = new Data('15032021');
        $this->assertSame('15032021', (string) $data);
    }
}
