<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentosPJ;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentosPJ;
use PHPUnit\Framework\TestCase;

final class LeitorRendimentosPJTest extends TestCase
{
    private GeradorRendimentosPJ $gerador;
    private LeitorRendimentosPJ $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorRendimentosPJ();
        $this->leitor = new LeitorRendimentosPJ();
    }

    public function testDeveRetornarTipoCorreto(): void
    {
        $this->assertSame(TipoRegistro::RENDIMENTOS_PJ, $this->leitor->suportaTipo());
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroRendimentosPJDTO(
            cpf: new Cpf('12345678901'),
            cnpjFontePagadora: new Cnpj('12345678000199'),
            nomeFontePagadora: 'EMPRESA EXEMPLO LTDA',
            rendimentosRecebidos: ValorMonetario::deCentavos(150000),
            contribPrevidenciaria: ValorMonetario::deCentavos(25000),
            decimoTerceiroSalario: ValorMonetario::deCentavos(12500),
            impostoRetidoFonte: ValorMonetario::deCentavos(18750),
            irrfDecimoTerceiro: ValorMonetario::deCentavos(1875),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(170, strlen($linha));

        /** @var RegistroRendimentosPJDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('12345678000199', $lido->cnpjFontePagadora->valor);
        $this->assertSame('EMPRESA EXEMPLO LTDA', $lido->nomeFontePagadora);
        $this->assertSame(150000, $lido->rendimentosRecebidos->centavos);
        $this->assertSame(25000, $lido->contribPrevidenciaria->centavos);
        $this->assertSame(12500, $lido->decimoTerceiroSalario->centavos);
        $this->assertSame(18750, $lido->impostoRetidoFonte->centavos);
        $this->assertSame(1875, $lido->irrfDecimoTerceiro->centavos);
    }

    public function testDeveRealizarRoundTripComValoresZerados(): void
    {
        $original = new RegistroRendimentosPJDTO(
            cpf: new Cpf('12345678901'),
            cnpjFontePagadora: new Cnpj('98765432000188'),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroRendimentosPJDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('98765432000188', $lido->cnpjFontePagadora->valor);
        $this->assertSame('', $lido->nomeFontePagadora);
        $this->assertSame(0, $lido->rendimentosRecebidos->centavos);
        $this->assertSame(0, $lido->contribPrevidenciaria->centavos);
        $this->assertSame(0, $lido->decimoTerceiroSalario->centavos);
        $this->assertSame(0, $lido->impostoRetidoFonte->centavos);
        $this->assertSame(0, $lido->irrfDecimoTerceiro->centavos);
    }
}
