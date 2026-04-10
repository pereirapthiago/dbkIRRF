<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorHeader;
use DbkIrrf\Infraestrutura\Leitor\LeitorHeader;
use PHPUnit\Framework\TestCase;

final class LeitorHeaderTest extends TestCase
{
    private GeradorHeader $gerador;
    private LeitorHeader $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorHeader();
        $this->leitor = new LeitorHeader();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            anoExercicio: 2026,
            anoCalendario: 2025,
            codigoVersao: '36',
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            codigoNaturezaOcupacao: '1100',
            nome: 'JORGE LUCAS DA SILVA MONTANO',
            uf: UnidadeFederativa::RJ,
            dataNascimento: Data::deDateTime(new \DateTime('2000-10-10')),
            estadoCivil: EstadoCivil::SOLTEIRO,
            codigoMunicipioIbge: '5877',
            cep: '25845060',
            cidade: 'PETROPOLIS',
            reciboDeclaracaoAnterior: '2345235423',
            impostoAPagar: ValorMonetario::deCentavos(1480109),
            cnpjFontePrincipal: new Cnpj('27865757000102'),
            cpfDependenteConjuge: new Cpf('13480293077'),
            dataNascimentoDependente: Data::deDateTime(new \DateTime('2021-03-15')),
            cpfMedicoTerceiro: new Cpf('66313835018'),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(1244, strlen($linha));

        /** @var RegistroHeaderDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('41653508000', $lido->cpf->valor);
        $this->assertSame(2026, $lido->anoExercicio);
        $this->assertSame(2025, $lido->anoCalendario);
        $this->assertSame('36', $lido->codigoVersao);
        $this->assertSame(TipoDeclaracao::ORIGINAL, $lido->tipoDeclaracao);
        $this->assertSame('1100', $lido->codigoNaturezaOcupacao);
        $this->assertSame('JORGE LUCAS DA SILVA MONTANO', $lido->nome);
        $this->assertSame(UnidadeFederativa::RJ, $lido->uf);
        $this->assertSame('10102000', $lido->dataNascimento->valor);
        $this->assertSame(EstadoCivil::SOLTEIRO, $lido->estadoCivil);
        $this->assertSame('5877', $lido->codigoMunicipioIbge);
        $this->assertSame('25845060', $lido->cep);
        $this->assertSame('PETROPOLIS', $lido->cidade);
        $this->assertSame('2345235423', $lido->reciboDeclaracaoAnterior);
        $this->assertSame(1480109, $lido->impostoAPagar->centavos);
        $this->assertSame('27865757000102', $lido->cnpjFontePrincipal->valor);
        $this->assertSame('13480293077', $lido->cpfDependenteConjuge->valor);
        $this->assertSame('15032021', $lido->dataNascimentoDependente->valor);
        $this->assertSame('66313835018', $lido->cpfMedicoTerceiro->valor);
    }

    public function testDeveRealizarRoundTripRetificadora(): void
    {
        $original = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: '2345235423',
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroHeaderDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame(TipoDeclaracao::RETIFICADORA, $lido->tipoDeclaracao);
        $this->assertSame('2345235423', $lido->reciboDeclaracaoAnterior);
    }

    public function testDeveLancarExcecaoParaLinhaComTamanhoIncorreto(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->leitor->ler('IRPF' . str_repeat(' ', 100));
    }
}
