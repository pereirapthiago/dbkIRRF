<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorBemDireito;
use DbkIrrf\Infraestrutura\Leitor\LeitorBemDireito;
use PHPUnit\Framework\TestCase;

final class LeitorBemDireitoTest extends TestCase
{
    private GeradorBemDireito $gerador;
    private LeitorBemDireito $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorBemDireito();
        $this->leitor = new LeitorBemDireito();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
            codigoItem: '11',
            flagExterior: '0',
            pais: '105',
            descricao: 'APARTAMENTO RESIDENCIAL 3 QUARTOS',
            valorAnterior: new ValorMonetario(35000000),
            valorAtual: new ValorMonetario(40000000),
            logradouro: 'RUA DAS FLORES',
            numero: '100',
            complemento: 'APTO 301',
            bairro: 'CENTRO',
            cep: '25845060',
            uf: 'RJ',
            codigoMunicipioIbge: '5877',
            municipio: 'PETROPOLIS',
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(1251, strlen($linha));

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('11', $lido->codigoItem);
        $this->assertSame('0', $lido->flagExterior);
        $this->assertSame('105', $lido->pais);
        $this->assertSame('APARTAMENTO RESIDENCIAL 3 QUARTOS', $lido->descricao);
        $this->assertSame(35000000, $lido->valorAnterior->centavos);
        $this->assertSame(40000000, $lido->valorAtual->centavos);
        $this->assertSame('RUA DAS FLORES', $lido->logradouro);
        $this->assertSame('100', $lido->numero);
        $this->assertSame('APTO 301', $lido->complemento);
        $this->assertSame('CENTRO', $lido->bairro);
        $this->assertSame('25845060', $lido->cep);
        $this->assertSame('RJ', $lido->uf);
        $this->assertSame('5877', $lido->codigoMunicipioIbge);
        $this->assertSame('PETROPOLIS', $lido->municipio);
    }

    public function testDevePreservarDadosBancarios(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
            agencia: '1234',
            dvConta: '5',
            numeroConta: '0000012345678',
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('1234', $lido->agencia);
        $this->assertSame('5', $lido->dvConta);
        $this->assertSame('0000012345678', $lido->numeroConta);
    }

    public function testDevePreservarDataAquisicaoERendavam(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
            codigoItem: '01',
            codigoGrupo: '02',
            dataAquisicao: new Data('15062023'),
            renavam: '12354654564',
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('15062023', $lido->dataAquisicao->valor);
        $this->assertSame('12354654564', $lido->renavam);
    }

    public function testDataAquisicaoVaziaDeveSerZerada(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertTrue($lido->dataAquisicao->eVazia());
        $this->assertSame('00000000000', $lido->renavam);
    }

    public function testDevePreservarInvestimentoExterior(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
            aplicFinancRendPerda: new ValorMonetario(500000),
            aplicFinancImpExterior: new ValorMonetario(75000),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame(500000, $lido->aplicFinancRendPerda->centavos);
        $this->assertSame(75000, $lido->aplicFinancImpExterior->centavos);
    }

    public function testDevePreservarAplicFinancPosicaoAlternativa(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
            aplicFinancRendPerdaAlt: new ValorMonetario(1200000),
            aplicFinancImpExteriorAlt: new ValorMonetario(80000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(1251, strlen($linha));

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame(1200000, $lido->aplicFinancRendPerdaAlt->centavos);
        $this->assertSame(80000, $lido->aplicFinancImpExteriorAlt->centavos);
    }

    public function testDevePreservarLucrosEDividendos(): void
    {
        $original = new RegistroBemDireitoDTO(
            cpf: new Cpf('12345678901'),
            lucrosDivValorRecebido: new ValorMonetario(5000000),
            lucrosDivImpostoPago: new ValorMonetario(750000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(1251, strlen($linha));

        /** @var RegistroBemDireitoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame(5000000, $lido->lucrosDivValorRecebido->centavos);
        $this->assertSame(750000, $lido->lucrosDivImpostoPago->centavos);
    }

    public function testDeveSuportarTipoBemDireito(): void
    {
        $this->assertSame(TipoRegistro::BEM_DIREITO, $this->leitor->suportaTipo());
    }
}
