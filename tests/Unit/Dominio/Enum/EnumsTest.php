<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\Enum;

use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\FlagSimNao;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\ValorObjeto\CodigoGrupoBem;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    public function testEstadoCivilDeveMapearValoresCorretos(): void
    {
        $this->assertSame('S', EstadoCivil::SOLTEIRO->value);
        $this->assertSame('C', EstadoCivil::CASADO->value);
        $this->assertSame('D', EstadoCivil::DIVORCIADO->value);
        $this->assertSame('V', EstadoCivil::VIUVO->value);
        $this->assertSame(EstadoCivil::SOLTEIRO, EstadoCivil::from('S'));
    }

    public function testUnidadeFederativaDeveTer27Estados(): void
    {
        $this->assertCount(27, UnidadeFederativa::cases());
        $this->assertSame('RJ', UnidadeFederativa::RJ->value);
        $this->assertSame(UnidadeFederativa::SP, UnidadeFederativa::from('SP'));
    }

    public function testTipoDeclaracaoDeveTerSufixoArquivo(): void
    {
        $this->assertSame('ORIGI', TipoDeclaracao::ORIGINAL->obterSufixoArquivo());
        $this->assertSame('RETIF', TipoDeclaracao::RETIFICADORA->obterSufixoArquivo());
        $this->assertSame('0', TipoDeclaracao::ORIGINAL->value);
        $this->assertSame('1', TipoDeclaracao::RETIFICADORA->value);
    }

    public function testTipoRegistroDeveRetornarDescricao(): void
    {
        $this->assertSame('Header / Cabecalho da declaracao', TipoRegistro::HEADER->obterDescricao());
        $this->assertSame('Bens e direitos', TipoRegistro::BEM_DIREITO->obterDescricao());
        $this->assertSame('Trailer / Totalizador', TipoRegistro::TRAILER->obterDescricao());
    }

    public function testCodigoDependenteDeveAceitarQualquerCodigo(): void
    {
        $filho = new CodigoDependente(CodigoDependente::FILHO_ATE_21);
        $this->assertSame('21', $filho->valor);
        $this->assertSame('Filho(a) ou enteado(a) ate 21 anos', $filho->obterDescricao());

        // Codigo desconhecido nao deve quebrar
        $desconhecido = new CodigoDependente('99');
        $this->assertSame('99', $desconhecido->valor);
        $this->assertSame('Codigo dependente 99', $desconhecido->obterDescricao());
    }

    public function testCodigoGrupoBemDeveAceitarQualquerCodigo(): void
    {
        $imoveis = new CodigoGrupoBem(CodigoGrupoBem::BENS_IMOVEIS);
        $this->assertSame('01', $imoveis->valor);
        $this->assertSame('Bens imoveis', $imoveis->obterDescricao());

        $depositos = new CodigoGrupoBem(CodigoGrupoBem::DEPOSITOS_VISTA_NUMERARIO);
        $this->assertSame('06', $depositos->valor);

        // Codigo novo da Receita nao deve quebrar
        $futuro = new CodigoGrupoBem('15');
        $this->assertSame('15', $futuro->valor);
        $this->assertSame('Grupo de bem 15', $futuro->obterDescricao());
    }

    public function testTipoBeneficiarioDeveMapearFlags(): void
    {
        $this->assertSame('T', TipoBeneficiario::TITULAR->value);
        $this->assertSame('D', TipoBeneficiario::DEPENDENTE->value);
    }

    public function testFlagSimNaoDeveMapearValores(): void
    {
        $this->assertSame('S', FlagSimNao::SIM->value);
        $this->assertSame('N', FlagSimNao::NAO->value);
    }

    public function testSubTipoInvestimentoDeveMapearCodigos(): void
    {
        $this->assertSame('1', SubTipoInvestimento::APLICACOES_FINANCEIRAS->value);
        $this->assertSame('2', SubTipoInvestimento::LUCROS_DIVIDENDOS->value);
    }
}
