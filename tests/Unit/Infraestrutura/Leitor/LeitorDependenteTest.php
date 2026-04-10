<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Infraestrutura\Gerador\GeradorDependente;
use DbkIrrf\Infraestrutura\Leitor\LeitorDependente;
use PHPUnit\Framework\TestCase;

final class LeitorDependenteTest extends TestCase
{
    private GeradorDependente $gerador;
    private LeitorDependente $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDependente();
        $this->leitor = new LeitorDependente();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroDependenteDTO(
            cpf: new Cpf('12345678901'),
            sequencial: 1,
            tipoDependente: new CodigoDependente('21'),
            nomeDependente: 'MARIA SILVA SANTOS',
            dataNascimento: new Data('15061990'),
            cpfDependente: new Cpf('98765432100'),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(224, strlen($linha));

        /** @var RegistroDependenteDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame(1, $lido->sequencial);
        $this->assertSame('21', $lido->tipoDependente->valor);
        $this->assertSame('MARIA SILVA SANTOS', $lido->nomeDependente);
        $this->assertSame('15061990', $lido->dataNascimento->valor);
        $this->assertSame('98765432100', $lido->cpfDependente->valor);
    }

    public function testDeveSuportarTipoDependente(): void
    {
        $this->assertSame(TipoRegistro::DEPENDENTE, $this->leitor->suportaTipo());
    }
}
