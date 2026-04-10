<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Gerador;

use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorDeducaoLegal;
use DbkIrrf\Infraestrutura\Gerador\GeradorImpostoPago;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentosMensais;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentosPJ;
use DbkIrrf\Infraestrutura\Leitor\LeitorDeducaoLegal;
use DbkIrrf\Infraestrutura\Leitor\LeitorImpostoPago;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentosMensais;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentosPJ;
use PHPUnit\Framework\TestCase;

final class GeradorFinanceirosTest extends TestCase
{
    // ========== Registro 21 - Rendimentos PJ ==========

    public function testReg21DeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = $this->criarRendimentoPJ();
        $linha = (new GeradorRendimentosPJ())->gerar($dto);

        $this->assertSame(170, strlen($linha));
    }

    public function testReg21DeveGerarCamposNasPosicoesCorretas(): void
    {
        $dto = $this->criarRendimentoPJ();
        $linha = (new GeradorRendimentosPJ())->gerar($dto);

        $this->assertSame('21', substr($linha, 0, 2));                         // tipo
        $this->assertSame('41653508000', substr($linha, 2, 11));               // cpf
        $this->assertSame('27865757000102', substr($linha, 13, 14));           // cnpj
        $this->assertSame('0000018000000', substr($linha, 87, 13));            // rendimentos
        $this->assertSame('0000001200000', substr($linha, 100, 13));           // contrib prev
        $this->assertSame('0000001200000', substr($linha, 113, 13));           // 13o salario
        $this->assertSame('0000005000000', substr($linha, 126, 13));           // IR retido
        $this->assertSame('0000000100000', substr($linha, 147, 13));           // IRRF 13o
    }

    public function testReg21RoundTrip(): void
    {
        $original = $this->criarRendimentoPJ();
        $linha = (new GeradorRendimentosPJ())->gerar($original);

        /** @var RegistroRendimentosPJDTO $lido */
        $lido = (new LeitorRendimentosPJ())->ler($linha);

        $this->assertSame('41653508000', $lido->cpf->valor);
        $this->assertSame('27865757000102', $lido->cnpjFontePagadora->valor);
        $this->assertSame('GLOBO COMUNICACAO E PARTICIPACOES S/A', $lido->nomeFontePagadora);
        $this->assertSame(18000000, $lido->rendimentosRecebidos->centavos);
        $this->assertSame(1200000, $lido->contribPrevidenciaria->centavos);
        $this->assertSame(1200000, $lido->decimoTerceiroSalario->centavos);
        $this->assertSame(5000000, $lido->impostoRetidoFonte->centavos);
        $this->assertSame(100000, $lido->irrfDecimoTerceiro->centavos);
    }

    public function testReg21RoundTripComCentavosQuebrados(): void
    {
        $dto = new RegistroRendimentosPJDTO(
            cpf: new Cpf('41653508000'),
            cnpjFontePagadora: new Cnpj('27865757000102'),
            nomeFontePagadora: 'TESTE',
            rendimentosRecebidos: ValorMonetario::deCentavos(25075628),
            contribPrevidenciaria: ValorMonetario::deCentavos(1588463),
            decimoTerceiroSalario: ValorMonetario::deCentavos(3219989),
            impostoRetidoFonte: ValorMonetario::deCentavos(6035466),
            irrfDecimoTerceiro: ValorMonetario::deCentavos(219433),
        );

        $linha = (new GeradorRendimentosPJ())->gerar($dto);
        /** @var RegistroRendimentosPJDTO $lido */
        $lido = (new LeitorRendimentosPJ())->ler($linha);

        $this->assertSame(250756.28, $lido->rendimentosRecebidos->emReais());
        $this->assertSame(15884.63, $lido->contribPrevidenciaria->emReais());
        $this->assertSame(32199.89, $lido->decimoTerceiroSalario->emReais());
        $this->assertSame(60354.66, $lido->impostoRetidoFonte->emReais());
        $this->assertSame(2194.33, $lido->irrfDecimoTerceiro->emReais());
    }

    // ========== Registro 22 - Rendimentos Mensais ==========

    public function testReg22DeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = $this->criarRendimentoMensal(1);
        $linha = (new GeradorRendimentosMensais())->gerar($dto);

        $this->assertSame(167, strlen($linha));
    }

    public function testReg22RoundTrip(): void
    {
        $dto = new RegistroRendimentosMensaisDTO(
            cpf: new Cpf('41653508000'),
            mesReferencia: 1,
            temporada: ValorMonetario::deCentavos(100000),
            outrosRendimentos: ValorMonetario::deCentavos(110000),
            exterior: ValorMonetario::deCentavos(300000),
            totalRendimentosMes: ValorMonetario::deCentavos(510000),
            darfPago: ValorMonetario::deCentavos(500000),
        );

        $linha = (new GeradorRendimentosMensais())->gerar($dto);
        /** @var RegistroRendimentosMensaisDTO $lido */
        $lido = (new LeitorRendimentosMensais())->ler($linha);

        $this->assertSame(1, $lido->mesReferencia);
        $this->assertSame(1000.00, $lido->temporada->emReais());
        $this->assertSame(1100.00, $lido->outrosRendimentos->emReais());
        $this->assertSame(3000.00, $lido->exterior->emReais());
        $this->assertSame(5100.00, $lido->totalRendimentosMes->emReais());
        $this->assertSame(5000.00, $lido->darfPago->emReais());
    }

    public function testReg22DeveSuportarTodos12Meses(): void
    {
        $gerador = new GeradorRendimentosMensais();
        $leitor = new LeitorRendimentosMensais();

        for ($mes = 1; $mes <= 12; $mes++) {
            $dto = $this->criarRendimentoMensal($mes);
            $linha = $gerador->gerar($dto);
            /** @var RegistroRendimentosMensaisDTO $lido */
            $lido = $leitor->ler($linha);

            $this->assertSame($mes, $lido->mesReferencia);
            $this->assertSame(167, strlen($linha));
        }
    }

    // ========== Registro 23 - Imposto Pago ==========

    public function testReg23DeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = new RegistroImpostoPagoDTO(
            cpf: new Cpf('41653508000'),
            codigo: '0001',
            valor: ValorMonetario::deCentavos(500000),
        );
        $linha = (new GeradorImpostoPago())->gerar($dto);

        $this->assertSame(40, strlen($linha));
    }

    public function testReg23RoundTrip(): void
    {
        $dto = new RegistroImpostoPagoDTO(
            cpf: new Cpf('41653508000'),
            codigo: '0001',
            valor: ValorMonetario::deCentavos(500000),
        );

        $linha = (new GeradorImpostoPago())->gerar($dto);
        /** @var RegistroImpostoPagoDTO $lido */
        $lido = (new LeitorImpostoPago())->ler($linha);

        $this->assertSame('41653508000', $lido->cpf->valor);
        $this->assertSame('0001', $lido->codigo);
        $this->assertSame(5000.00, $lido->valor->emReais());
    }

    // ========== Registro 24 - Deducoes Legais ==========

    public function testReg24DeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = new RegistroDeducaoLegalDTO(
            cpf: new Cpf('41653508000'),
            codigoDeducao: '0001',
            valor: ValorMonetario::deCentavos(1200000),
        );
        $linha = (new GeradorDeducaoLegal())->gerar($dto);

        $this->assertSame(40, strlen($linha));
    }

    public function testReg24RoundTripTresCodigos(): void
    {
        $gerador = new GeradorDeducaoLegal();
        $leitor = new LeitorDeducaoLegal();

        $codigos = [
            ['0001', 1200000, 'Previdencia oficial'],
            ['0006', 1000000, 'Tributacao exclusiva'],
            ['0007', 100000, 'RRA'],
        ];

        foreach ($codigos as [$codigo, $centavos, $descricao]) {
            $dto = new RegistroDeducaoLegalDTO(
                cpf: new Cpf('41653508000'),
                codigoDeducao: $codigo,
                valor: ValorMonetario::deCentavos($centavos),
            );

            $linha = $gerador->gerar($dto);
            /** @var RegistroDeducaoLegalDTO $lido */
            $lido = $leitor->ler($linha);

            $this->assertSame($codigo, $lido->codigoDeducao, "Falha no codigo {$descricao}");
            $this->assertSame($centavos, $lido->valor->centavos, "Falha no valor {$descricao}");
        }
    }

    // ========== Helpers ==========

    private function criarRendimentoPJ(): RegistroRendimentosPJDTO
    {
        return new RegistroRendimentosPJDTO(
            cpf: new Cpf('41653508000'),
            cnpjFontePagadora: new Cnpj('27865757000102'),
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            rendimentosRecebidos: ValorMonetario::deCentavos(18000000),
            contribPrevidenciaria: ValorMonetario::deCentavos(1200000),
            decimoTerceiroSalario: ValorMonetario::deCentavos(1200000),
            impostoRetidoFonte: ValorMonetario::deCentavos(5000000),
            irrfDecimoTerceiro: ValorMonetario::deCentavos(100000),
        );
    }

    private function criarRendimentoMensal(int $mes): RegistroRendimentosMensaisDTO
    {
        return new RegistroRendimentosMensaisDTO(
            cpf: new Cpf('41653508000'),
            mesReferencia: $mes,
            exterior: ValorMonetario::deCentavos(1500000),
            totalRendimentosMes: ValorMonetario::deCentavos(1500000),
            darfPago: ValorMonetario::deCentavos(150000),
        );
    }
}
