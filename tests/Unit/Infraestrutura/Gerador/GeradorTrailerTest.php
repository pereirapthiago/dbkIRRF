<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Gerador;

use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Infraestrutura\Gerador\GeradorTrailer;
use PHPUnit\Framework\TestCase;

final class GeradorTrailerTest extends TestCase
{
    private GeradorTrailer $gerador;

    protected function setUp(): void
    {
        $this->gerador = new GeradorTrailer();
    }

    public function testDeveGerarLinhaComTamanhoCorreto(): void
    {
        $dto = new RegistroTrailerDTO(
            cpf: new Cpf('41653508000'),
            totalRegistros: 28,
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame(449, strlen($linha));
    }

    public function testDeveSuportarTipoT9(): void
    {
        $this->assertSame(TipoRegistro::TRAILER, $this->gerador->suportaTipo());
    }

    public function testDeveGerarTipoRegistroNoInicio(): void
    {
        $dto = new RegistroTrailerDTO(
            cpf: new Cpf('41653508000'),
            totalRegistros: 28,
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('T9', substr($linha, 0, 2));
    }

    public function testDeveGerarTotalRegistrosComOitoDigitos(): void
    {
        $dto = new RegistroTrailerDTO(
            cpf: new Cpf('41653508000'),
            totalRegistros: 28,
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('00000028', substr($linha, 13, 8)); // pos 14-21
    }

    public function testDeveGerarCpfNaPosicao3(): void
    {
        $dto = new RegistroTrailerDTO(
            cpf: new Cpf('41653508000'),
            totalRegistros: 28,
        );
        $linha = $this->gerador->gerar($dto);

        $this->assertSame('41653508000', substr($linha, 2, 11)); // pos 3-13
    }
}
