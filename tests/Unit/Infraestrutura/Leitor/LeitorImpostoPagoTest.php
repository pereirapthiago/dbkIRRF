<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorImpostoPago;
use DbkIrrf\Infraestrutura\Leitor\LeitorImpostoPago;
use PHPUnit\Framework\TestCase;

final class LeitorImpostoPagoTest extends TestCase
{
    private GeradorImpostoPago $gerador;
    private LeitorImpostoPago $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorImpostoPago();
        $this->leitor = new LeitorImpostoPago();
    }

    public function testDeveRetornarTipoCorreto(): void
    {
        $this->assertSame(TipoRegistro::IMPOSTO_PAGO, $this->leitor->suportaTipo());
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroImpostoPagoDTO(
            cpf: new Cpf('12345678901'),
            codigo: '0001',
            valor: ValorMonetario::deCentavos(150000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(40, strlen($linha));

        /** @var RegistroImpostoPagoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('0001', $lido->codigo);
        $this->assertSame(150000, $lido->valor->centavos);
    }

    public function testDeveRealizarRoundTripComValorZerado(): void
    {
        $original = new RegistroImpostoPagoDTO(
            cpf: new Cpf('12345678901'),
            codigo: '0003',
            valor: ValorMonetario::deCentavos(0),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroImpostoPagoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('0003', $lido->codigo);
        $this->assertSame(0, $lido->valor->centavos);
    }
}
