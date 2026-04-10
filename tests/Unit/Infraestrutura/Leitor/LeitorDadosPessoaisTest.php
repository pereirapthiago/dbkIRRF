<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\Enum\FlagSimNao;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Infraestrutura\Gerador\GeradorDadosPessoais;
use DbkIrrf\Infraestrutura\Leitor\LeitorDadosPessoais;
use PHPUnit\Framework\TestCase;

final class LeitorDadosPessoaisTest extends TestCase
{
    private GeradorDadosPessoais $gerador;
    private LeitorDadosPessoais $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDadosPessoais();
        $this->leitor = new LeitorDadosPessoais();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroDadosPessoaisDTO(
            cpf: new Cpf('41653508000'),
            nome: 'JORGE LUCAS DA SILVA MONTANO',
            tipoLogradouro: 'RUA',
            logradouro: 'AV KOELER',
            numero: '260',
            complemento: 'CASA',
            bairro: 'CENTRO',
            cep: '25845060',
            codigoMunicipioIbge: '5877',
            municipio: 'PETROPOLIS',
            uf: UnidadeFederativa::RJ,
            email: 'JORGEMONTANO@GMAIL.COM',
            dddCelular: '24',
            celular: '999999999',
            dddFixo: '24',
            telefoneFixo: '22429249',
            dataNascimento: Data::deDateTime(new \DateTime('2000-10-10')),
            codigoOcupacao: '261',
            cpfConjuge: new Cpf('65989864212'),
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            flagAlteracaoCadastral: FlagSimNao::NAO,
            reciboDeclaracaoAnterior: '2345235423',
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(930, strlen($linha));

        /** @var RegistroDadosPessoaisDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('41653508000', $lido->cpf->valor);
        $this->assertSame('JORGE LUCAS DA SILVA MONTANO', $lido->nome);
        $this->assertSame('RUA', $lido->tipoLogradouro);
        $this->assertSame('AV KOELER', $lido->logradouro);
        $this->assertSame('260', $lido->numero);
        $this->assertSame('CASA', $lido->complemento);
        $this->assertSame('CENTRO', $lido->bairro);
        $this->assertSame('25845060', $lido->cep);
        $this->assertSame('5877', $lido->codigoMunicipioIbge);
        $this->assertSame('PETROPOLIS', $lido->municipio);
        $this->assertSame(UnidadeFederativa::RJ, $lido->uf);
        $this->assertSame('JORGEMONTANO@GMAIL.COM', $lido->email);
        $this->assertSame('24', $lido->dddCelular);
        $this->assertSame('999999999', $lido->celular);
        $this->assertSame('10102000', $lido->dataNascimento->valor);
        $this->assertSame('261', $lido->codigoOcupacao);
        $this->assertSame('65989864212', $lido->cpfConjuge->valor);
        $this->assertSame(TipoDeclaracao::ORIGINAL, $lido->tipoDeclaracao);
        $this->assertSame('2345235423', $lido->reciboDeclaracaoAnterior);
    }

    public function testDeveRealizarRoundTripRetificadora(): void
    {
        $original = new RegistroDadosPessoaisDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: '2345235423',
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroDadosPessoaisDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame(TipoDeclaracao::RETIFICADORA, $lido->tipoDeclaracao);
        $this->assertSame('2345235423', $lido->reciboDeclaracaoAnterior);
        $this->assertSame('S', substr($linha, 388, 1)); // pos 389
    }

    public function testDevePreservarResidenciaPais(): void
    {
        $original = new RegistroDadosPessoaisDTO(
            cpf: new Cpf('41653508000'),
            flagResidenciaPais: '1',
            dataResidenciaPais: Data::deDateTime(new \DateTime('2025-03-10')),
        );

        $linha = $this->gerador->gerar($original);

        /** @var RegistroDadosPessoaisDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('1', $lido->flagResidenciaPais);
        $this->assertSame('10032025', $lido->dataResidenciaPais->valor);
    }
}
