<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\Enum;

use DbkIrrf\Dominio\Enum\TipoRegistro;
use PHPUnit\Framework\TestCase;

final class TipoRegistroTest extends TestCase
{
    public function testDeveRetornarTamanhoCorretoParaCadaRegistro(): void
    {
        $this->assertSame(1244, TipoRegistro::HEADER->obterTamanhoLinha());
        $this->assertSame(930, TipoRegistro::DADOS_PESSOAIS->obterTamanhoLinha());
        $this->assertSame(170, TipoRegistro::RENDIMENTOS_PJ->obterTamanhoLinha());
        $this->assertSame(167, TipoRegistro::RENDIMENTOS_MENSAIS->obterTamanhoLinha());
        $this->assertSame(40, TipoRegistro::IMPOSTO_PAGO->obterTamanhoLinha());
        $this->assertSame(40, TipoRegistro::DEDUCAO_LEGAL->obterTamanhoLinha());
        $this->assertSame(224, TipoRegistro::DEPENDENTE->obterTamanhoLinha());
        $this->assertSame(671, TipoRegistro::PAGAMENTO->obterTamanhoLinha());
        $this->assertSame(1251, TipoRegistro::BEM_DIREITO->obterTamanhoLinha());
        $this->assertSame(576, TipoRegistro::DIVIDA->obterTamanhoLinha());
        $this->assertSame(103, TipoRegistro::INVESTIMENTO_EXTERIOR->obterTamanhoLinha());
        $this->assertSame(216, TipoRegistro::RRA->obterTamanhoLinha());
        $this->assertSame(144, TipoRegistro::RENDIMENTO_ISENTO->obterTamanhoLinha());
        $this->assertSame(131, TipoRegistro::TRIBUTACAO_EXCLUSIVA->obterTamanhoLinha());
        $this->assertSame(449, TipoRegistro::TRAILER->obterTamanhoLinha());
    }

    public function testDeveRetornarTamanhoCodigoCorreto(): void
    {
        $this->assertSame(4, TipoRegistro::HEADER->obterTamanhoCodigo());
        $this->assertSame(2, TipoRegistro::DADOS_PESSOAIS->obterTamanhoCodigo());
        $this->assertSame(2, TipoRegistro::TRAILER->obterTamanhoCodigo());
    }

    public function testDeveIdentificarTipoPorLinha(): void
    {
        $this->assertSame(TipoRegistro::HEADER, TipoRegistro::identificarPorLinha('IRPF    2026...'));
        $this->assertSame(TipoRegistro::DADOS_PESSOAIS, TipoRegistro::identificarPorLinha('1641653508000...'));
        $this->assertSame(TipoRegistro::RENDIMENTOS_PJ, TipoRegistro::identificarPorLinha('2141653508000...'));
        $this->assertSame(TipoRegistro::TRAILER, TipoRegistro::identificarPorLinha('T941653508000...'));
    }

    public function testDeveTer16TiposDeRegistro(): void
    {
        $this->assertCount(17, TipoRegistro::cases());
    }
}
