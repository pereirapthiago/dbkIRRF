<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentosMensais;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentosMensais;
use PHPUnit\Framework\TestCase;

final class LeitorRendimentosMensaisTest extends TestCase
{
    private GeradorRendimentosMensais $gerador;
    private LeitorRendimentosMensais $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorRendimentosMensais();
        $this->leitor = new LeitorRendimentosMensais();
    }

    public function testDeveRetornarTipoCorreto(): void
    {
        $this->assertSame(TipoRegistro::RENDIMENTOS_MENSAIS, $this->leitor->suportaTipo());
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroRendimentosMensaisDTO(
            cpf: new Cpf('12345678901'),
            mesReferencia: 3,
            rendNaoAssalariado: ValorMonetario::deCentavos(500000),
            temporada: ValorMonetario::deCentavos(100000),
            outrosRendimentos: ValorMonetario::deCentavos(75000),
            exterior: ValorMonetario::deCentavos(200000),
            previdencia: ValorMonetario::deCentavos(55000),
            dependentes: ValorMonetario::deCentavos(18956),
            pensaoAlimenticia: ValorMonetario::deCentavos(30000),
            livroCaixa: ValorMonetario::deCentavos(12000),
            totalRendimentosMes: ValorMonetario::deCentavos(875000),
            darfPago: ValorMonetario::deCentavos(131250),
            flagNS: 'S',
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(167, strlen($linha));

        /** @var RegistroRendimentosMensaisDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame(3, $lido->mesReferencia);
        $this->assertSame('S', $lido->flagNS);
        $this->assertSame(500000, $lido->rendNaoAssalariado->centavos);
        $this->assertSame(100000, $lido->temporada->centavos);
        $this->assertSame(75000, $lido->outrosRendimentos->centavos);
        $this->assertSame(200000, $lido->exterior->centavos);
        $this->assertSame(55000, $lido->previdencia->centavos);
        $this->assertSame(18956, $lido->dependentes->centavos);
        $this->assertSame(30000, $lido->pensaoAlimenticia->centavos);
        $this->assertSame(12000, $lido->livroCaixa->centavos);
        $this->assertSame(875000, $lido->totalRendimentosMes->centavos);
        $this->assertSame(131250, $lido->darfPago->centavos);
    }

    public function testDeveRealizarRoundTripComValoresZerados(): void
    {
        $original = new RegistroRendimentosMensaisDTO(
            cpf: new Cpf('12345678901'),
            mesReferencia: 1,
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroRendimentosMensaisDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame(1, $lido->mesReferencia);
        $this->assertSame('N', $lido->flagNS);
        $this->assertSame(0, $lido->rendNaoAssalariado->centavos);
        $this->assertSame(0, $lido->temporada->centavos);
        $this->assertSame(0, $lido->outrosRendimentos->centavos);
        $this->assertSame(0, $lido->exterior->centavos);
        $this->assertSame(0, $lido->previdencia->centavos);
        $this->assertSame(0, $lido->dependentes->centavos);
        $this->assertSame(0, $lido->pensaoAlimenticia->centavos);
        $this->assertSame(0, $lido->livroCaixa->centavos);
        $this->assertSame(0, $lido->totalRendimentosMes->centavos);
        $this->assertSame(0, $lido->darfPago->centavos);
    }
}
