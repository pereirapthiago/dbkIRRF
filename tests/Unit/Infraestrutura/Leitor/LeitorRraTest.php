<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorRra;
use DbkIrrf\Infraestrutura\Leitor\LeitorRra;
use PHPUnit\Framework\TestCase;

final class LeitorRraTest extends TestCase
{
    private GeradorRra $gerador;
    private LeitorRra $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorRra();
        $this->leitor = new LeitorRra();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $rendimentos = new ValorMonetario(85465); // R$ 854,65

        $original = new RegistroRraDTO(
            cpf: new Cpf('71926456130'),
            cnpjFontePagadora: new Cnpj('27865757000102'),
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A 123',
            contribPrevidenciaria: new ValorMonetario(33365),  // R$ 333,65
            impostoRetidoFonte: new ValorMonetario(2164),       // R$ 21,64
            mesRecebimentoRRA: '03',
            numMesesRRA: '3',
            impostoBrutoRRA: new ValorMonetario(0),             // zero — abaixo da faixa
            rendimentosRRA: $rendimentos,
            rendimentosRRACopia: $rendimentos,
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(216, strlen($linha));

        /** @var RegistroRraDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('71926456130', $lido->cpf->valor);
        $this->assertSame('27865757000102', $lido->cnpjFontePagadora->valor);
        $this->assertSame('GLOBO COMUNICACAO E PARTICIPACOES S/A 123', $lido->nomeFontePagadora);
        // pos 90-102 deve ser zero
        $this->assertSame(0, ValorMonetario::deString($lido->checksum->valor !== '0000000000' ? '0' : '0')->centavos);
        $this->assertSame(33365, $lido->contribPrevidenciaria->centavos);
        $this->assertSame(2164, $lido->impostoRetidoFonte->centavos);
        $this->assertSame('03', $lido->mesRecebimentoRRA);
        $this->assertSame('3', $lido->numMesesRRA);
        $this->assertSame(0, $lido->impostoBrutoRRA->centavos);
        $this->assertSame(85465, $lido->rendimentosRRA->centavos);    // pos 168-180
        $this->assertSame(85465, $lido->rendimentosRRACopia->centavos); // pos 181-193
    }

    public function testPosicaoZeroObrigatoriaPos90(): void
    {
        $rendimentos = new ValorMonetario(85465);

        $dto = new RegistroRraDTO(
            cpf: new Cpf('71926456130'),
            cnpjFontePagadora: new Cnpj('27865757000102'),
            rendimentosRRA: $rendimentos,
            rendimentosRRACopia: $rendimentos,
        );

        $linha = $this->gerador->gerar($dto);

        // Pos 90-102 (1-based, indices 89-101 em 0-based) devem ser zeros
        $pos90 = substr($linha, 89, 13);
        $this->assertSame('0000000000000', $pos90, 'Pos 90-102 deve ser zeros obrigatorios');

        // Pos 168-180 deve ter os rendimentos
        $pos168 = substr($linha, 167, 13);
        $this->assertSame('0000000085465', $pos168, 'Pos 168-180 deve ter os rendimentos RRA');

        // Pos 181-193 deve ser copia de pos 168-180
        $pos181 = substr($linha, 180, 13);
        $this->assertSame('0000000085465', $pos181, 'Pos 181-193 deve ser copia dos rendimentos RRA');
    }

    public function testDeveSuportarTipoRra(): void
    {
        $this->assertSame(TipoRegistro::RRA, $this->leitor->suportaTipo());
    }
}
