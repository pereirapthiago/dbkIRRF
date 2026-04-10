<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Gerador;

use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\Enum\FlagSimNao;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Infraestrutura\Gerador\GeradorDadosPessoais;
use PHPUnit\Framework\TestCase;

final class GeradorDadosPessoaisTest extends TestCase
{
    private GeradorDadosPessoais $gerador;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDadosPessoais();
    }

    public function testDeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = $this->criarDadosPessoaisBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame(930, strlen($linha));
    }

    public function testDeveSuportarTipoR16(): void
    {
        $this->assertSame(TipoRegistro::DADOS_PESSOAIS, $this->gerador->suportaTipo());
    }

    public function testDeveGerarTipoRegistroNoInicio(): void
    {
        $dto = $this->criarDadosPessoaisBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('16', substr($linha, 0, 2));
    }

    public function testDeveGerarCpfNaPosicao3(): void
    {
        $dto = $this->criarDadosPessoaisBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('41653508000', substr($linha, 2, 11)); // pos 3-13
    }

    public function testDeveGerarEnderecoCompleto(): void
    {
        $dto = $this->criarDadosPessoaisBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('RUA', rtrim(substr($linha, 73, 15)));     // pos 74-88
        $this->assertSame('AV KOELER', rtrim(substr($linha, 88, 40)));  // pos 89-128
        $this->assertSame('260', rtrim(substr($linha, 128, 6)));        // pos 129-134
        $this->assertSame('25845060', substr($linha, 174, 8));          // pos 175-182
        $this->assertSame('PETROPOLIS', rtrim(substr($linha, 187, 40)));// pos 188-227
        $this->assertSame('RJ', substr($linha, 227, 2));                // pos 228-229
    }

    public function testDeveGerarFlagRetificadoraCorretamente(): void
    {
        $dtoOrig = $this->criarDadosPessoaisBase();
        $linhaOrig = $this->gerador->gerar($dtoOrig);

        $this->assertSame('N', substr($linhaOrig, 388, 1)); // pos 389 = "N" (original)

        $dtoRetif = new RegistroDadosPessoaisDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: '2345235423',
        );
        $linhaRetif = $this->gerador->gerar($dtoRetif);

        $this->assertSame('S', substr($linhaRetif, 388, 1)); // pos 389 = "S" (retificadora)
        $this->assertSame('2345235423', substr($linhaRetif, 391, 10)); // pos 392-401
        $this->assertSame('          ', substr($linhaRetif, 443, 10)); // pos 444-453 vazio
    }

    public function testDeveGerarContatosCorretos(): void
    {
        $dto = $this->criarDadosPessoaisBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('24', substr($linha, 485, 2));        // pos 486-487 DDD
        $this->assertSame('999999999', substr($linha, 487, 9)); // pos 488-496 celular
    }

    private function criarDadosPessoaisBase(): RegistroDadosPessoaisDTO
    {
        return new RegistroDadosPessoaisDTO(
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
        );
    }
}
