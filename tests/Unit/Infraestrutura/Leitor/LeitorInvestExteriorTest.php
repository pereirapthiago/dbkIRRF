<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorInvestExterior;
use DbkIrrf\Infraestrutura\Leitor\LeitorInvestExterior;
use PHPUnit\Framework\TestCase;

final class LeitorInvestExteriorTest extends TestCase
{
    private GeradorInvestExterior $gerador;
    private LeitorInvestExterior $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorInvestExterior();
        $this->leitor = new LeitorInvestExterior();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroInvestExteriorDTO(
            cpf: new Cpf('12345678901'),
            idBem: '00001',
            sequencialDetalhe: '00001',
            subTipo: SubTipoInvestimento::APLICACOES_FINANCEIRAS,
            rendimentoValor: new ValorMonetario(150000),
            impostoDevido15: new ValorMonetario(22500),
            impostoPagoExterior: new ValorMonetario(10000),
            campoMonetario4: new ValorMonetario(5000),
            campoMonetario5: new ValorMonetario(3000),
            grupoBem: '07',
            codigoItem: '99',
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(103, strlen($linha));

        /** @var RegistroInvestExteriorDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('00001', $lido->idBem);
        $this->assertSame('00001', $lido->sequencialDetalhe);
        $this->assertSame(SubTipoInvestimento::APLICACOES_FINANCEIRAS, $lido->subTipo);
        $this->assertSame(150000, $lido->rendimentoValor->centavos);
        $this->assertSame(22500, $lido->impostoDevido15->centavos);
        $this->assertSame(10000, $lido->impostoPagoExterior->centavos);
        $this->assertSame(5000, $lido->campoMonetario4->centavos);
        $this->assertSame(3000, $lido->campoMonetario5->centavos);
        $this->assertSame('07', $lido->grupoBem);
        $this->assertSame('99', $lido->codigoItem);
    }

    public function testDeveSuportarTipoInvestimentoExterior(): void
    {
        $this->assertSame(TipoRegistro::INVESTIMENTO_EXTERIOR, $this->leitor->suportaTipo());
    }
}
