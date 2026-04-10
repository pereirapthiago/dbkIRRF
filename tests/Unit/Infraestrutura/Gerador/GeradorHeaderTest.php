<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Gerador;

use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorHeader;
use PHPUnit\Framework\TestCase;

final class GeradorHeaderTest extends TestCase
{
    private GeradorHeader $gerador;

    protected function setUp(): void
    {
        $this->gerador = new GeradorHeader();
    }

    public function testDeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = $this->criarHeaderBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame(1244, strlen($linha));
    }

    public function testDeveSuportarTipoIRPF(): void
    {
        $this->assertSame(TipoRegistro::HEADER, $this->gerador->suportaTipo());
    }

    public function testDeveGerarIdentificadorIRPFNoInicio(): void
    {
        $dto = $this->criarHeaderBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('IRPF', substr($linha, 0, 4));
    }

    public function testDeveGerarAnoExercicioECalendarioCorretos(): void
    {
        $dto = $this->criarHeaderBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('2026', substr($linha, 8, 4));   // pos 9-12
        $this->assertSame('2025', substr($linha, 12, 4));  // pos 13-16
    }

    public function testDeveGerarCpfNaPosicaoCorreta(): void
    {
        $dto = $this->criarHeaderBase();
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('41653508000', substr($linha, 21, 11)); // pos 22-32
        $this->assertSame('41653508000', substr($linha, 254, 11)); // pos 255-265 (repetido)
    }

    public function testDeveGerarNomePadComEspacos(): void
    {
        $dto = $this->criarHeaderBase();
        $linha = $this->gerador->gerar($dto);

        $nome = substr($linha, 39, 60); // pos 40-99
        $this->assertSame('JORGE LUCAS DA SILVA MONTANO', rtrim($nome));
        $this->assertSame(60, strlen($nome));
    }

    public function testDeveGerarDeclaracaoOriginalComReciboNaPosicao204(): void
    {
        $dto = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            reciboDeclaracaoAnterior: '2345235423',
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('0', substr($linha, 20, 1));                // pos 21 = "0" (original)
        $this->assertSame('          ', substr($linha, 123, 10));      // pos 124-133 vazio
        $this->assertSame('2345235423', substr($linha, 203, 10));      // pos 204-213 com recibo
    }

    public function testDeveGerarDeclaracaoRetificadoraComReciboNaPosicao124(): void
    {
        $dto = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: '2345235423',
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('1', substr($linha, 20, 1));                // pos 21 = "1" (retificadora)
        $this->assertSame('2345235423', substr($linha, 123, 10));      // pos 124-133 com recibo
        $this->assertSame('          ', substr($linha, 203, 10));      // pos 204-213 vazio
    }

    public function testDeveGerarImpostoAPagarComSeteCasas(): void
    {
        $dto = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            impostoAPagar: ValorMonetario::deCentavos(1480109),
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('1480109', substr($linha, 246, 7)); // pos 247-253
    }

    public function testDeveGerarCnpjFontePrincipal(): void
    {
        $dto = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            cnpjFontePrincipal: new Cnpj('27865757000102'),
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('27865757000102', substr($linha, 330, 14)); // pos 331-344
    }

    public function testDeveGerarChecksumNosUltimos10Digitos(): void
    {
        $dto = $this->criarHeaderBase();
        $linha = $this->gerador->gerar($dto);

        $checksum = substr($linha, -10);
        $this->assertSame('0000000000', $checksum); // placeholder
    }

    private function criarHeaderBase(): RegistroHeaderDTO
    {
        return new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            anoExercicio: 2026,
            anoCalendario: 2025,
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            nome: 'JORGE LUCAS DA SILVA MONTANO',
            uf: UnidadeFederativa::RJ,
            dataNascimento: Data::deDateTime(new \DateTime('2000-10-10')),
            estadoCivil: EstadoCivil::SOLTEIRO,
            codigoMunicipioIbge: '5877',
            cep: '25845060',
            cidade: 'PETROPOLIS',
            cnpjFontePrincipal: new Cnpj('27865757000102'),
            impostoAPagar: ValorMonetario::deCentavos(1480109),
        );
    }
}
