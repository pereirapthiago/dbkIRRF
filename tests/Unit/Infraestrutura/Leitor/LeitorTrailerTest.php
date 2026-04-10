<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Infraestrutura\Gerador\GeradorTrailer;
use DbkIrrf\Infraestrutura\Leitor\LeitorTrailer;
use PHPUnit\Framework\TestCase;

final class LeitorTrailerTest extends TestCase
{
    private GeradorTrailer $gerador;
    private LeitorTrailer $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorTrailer();
        $this->leitor = new LeitorTrailer();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $contadores = str_pad('00000015', 418, '0', STR_PAD_RIGHT);

        $original = new RegistroTrailerDTO(
            cpf: new Cpf('12345678901'),
            totalRegistros: 15,
            contadoresRaw: $contadores,
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(449, strlen($linha));

        /** @var RegistroTrailerDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame(15, $lido->totalRegistros);
        $this->assertSame($contadores, $lido->contadoresRaw);
    }

    public function testDeveSuportarTipoTrailer(): void
    {
        $this->assertSame(TipoRegistro::TRAILER, $this->leitor->suportaTipo());
    }
}
