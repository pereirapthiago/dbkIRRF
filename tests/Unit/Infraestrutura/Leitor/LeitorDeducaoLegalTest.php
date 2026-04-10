<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorDeducaoLegal;
use DbkIrrf\Infraestrutura\Leitor\LeitorDeducaoLegal;
use PHPUnit\Framework\TestCase;

final class LeitorDeducaoLegalTest extends TestCase
{
    private GeradorDeducaoLegal $gerador;
    private LeitorDeducaoLegal $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDeducaoLegal();
        $this->leitor = new LeitorDeducaoLegal();
    }

    public function testDeveRetornarTipoCorreto(): void
    {
        $this->assertSame(TipoRegistro::DEDUCAO_LEGAL, $this->leitor->suportaTipo());
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroDeducaoLegalDTO(
            cpf: new Cpf('12345678901'),
            codigoDeducao: '0001',
            valor: ValorMonetario::deCentavos(150000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(40, strlen($linha));

        /** @var RegistroDeducaoLegalDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('0001', $lido->codigoDeducao);
        $this->assertSame(150000, $lido->valor->centavos);
    }

    public function testDeveRealizarRoundTripComCodigoDiferente(): void
    {
        $original = new RegistroDeducaoLegalDTO(
            cpf: new Cpf('12345678901'),
            codigoDeducao: '0006',
            valor: ValorMonetario::deCentavos(87530),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroDeducaoLegalDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('0006', $lido->codigoDeducao);
        $this->assertSame(87530, $lido->valor->centavos);
    }

    public function testDeveRealizarRoundTripComValorZerado(): void
    {
        $original = new RegistroDeducaoLegalDTO(
            cpf: new Cpf('12345678901'),
            codigoDeducao: '0007',
            valor: ValorMonetario::deCentavos(0),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroDeducaoLegalDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('0007', $lido->codigoDeducao);
        $this->assertSame(0, $lido->valor->centavos);
    }
}
