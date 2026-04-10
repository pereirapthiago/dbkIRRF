<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorDivida;
use DbkIrrf\Infraestrutura\Leitor\LeitorDivida;
use PHPUnit\Framework\TestCase;

final class LeitorDividaTest extends TestCase
{
    private GeradorDivida $gerador;
    private LeitorDivida $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDivida();
        $this->leitor = new LeitorDivida();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroDividaDTO(
            cpf: new Cpf('12345678901'),
            codigoDivida: '11',
            descricao: 'FINANCIAMENTO HABITACIONAL CAIXA ECONOMICA FEDERAL',
            saldoAnterior: new ValorMonetario(25000000),
            saldoAtual: new ValorMonetario(22000000),
            valorPagoAno: new ValorMonetario(3000000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(576, strlen($linha));

        /** @var RegistroDividaDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('11', $lido->codigoDivida);
        $this->assertSame('FINANCIAMENTO HABITACIONAL CAIXA ECONOMICA FEDERAL', $lido->descricao);
        $this->assertSame(25000000, $lido->saldoAnterior->centavos);
        $this->assertSame(22000000, $lido->saldoAtual->centavos);
        $this->assertSame(3000000, $lido->valorPagoAno->centavos);
    }

    public function testDeveSuportarTipoDivida(): void
    {
        $this->assertSame(TipoRegistro::DIVIDA, $this->leitor->suportaTipo());
    }
}
