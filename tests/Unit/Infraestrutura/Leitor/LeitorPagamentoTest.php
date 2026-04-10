<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorPagamento;
use DbkIrrf\Infraestrutura\Leitor\LeitorPagamento;
use PHPUnit\Framework\TestCase;

final class LeitorPagamentoTest extends TestCase
{
    private GeradorPagamento $gerador;
    private LeitorPagamento $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorPagamento();
        $this->leitor = new LeitorPagamento();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroPagamentoDTO(
            cpf: new Cpf('12345678901'),
            codigoPagamento: '26',
            cpfCnpjBeneficiario: '98765432100',
            nomeBeneficiario: 'CLINICA MEDICA SAUDE LTDA',
            valorPago: new ValorMonetario(150000),
            parcelaNaoDedutivel: new ValorMonetario(30000),
            sequencial: '1',
            flagTitularDependente: 'T',
            descricao: 'CONSULTA MEDICA ORTOPEDISTA',
            codigoPais: '000',
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(671, strlen($linha));

        /** @var RegistroPagamentoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('12345678901', $lido->cpf->valor);
        $this->assertSame('26', $lido->codigoPagamento);
        $this->assertSame('98765432100', $lido->cpfCnpjBeneficiario);
        $this->assertSame('CLINICA MEDICA SAUDE LTDA', $lido->nomeBeneficiario);
        $this->assertSame(150000, $lido->valorPago->centavos);
        $this->assertSame(30000, $lido->parcelaNaoDedutivel->centavos);
        $this->assertSame('1', $lido->sequencial);
        $this->assertSame('T', $lido->flagTitularDependente);
        $this->assertSame('CONSULTA MEDICA ORTOPEDISTA', $lido->descricao);
        $this->assertSame('000', $lido->codigoPais);
    }

    public function testDeveSuportarCnpj14Digitos(): void
    {
        $original = new RegistroPagamentoDTO(
            cpf: new Cpf('12345678901'),
            cpfCnpjBeneficiario: '15451318789764',
            nomeBeneficiario: 'EMPRESA LTDA',
            valorPago: new ValorMonetario(100000),
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(671, strlen($linha));

        /** @var RegistroPagamentoDTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('15451318789764', $lido->cpfCnpjBeneficiario);
    }

    public function testDeveSuportarTipoPagamento(): void
    {
        $this->assertSame(TipoRegistro::PAGAMENTO, $this->leitor->suportaTipo());
    }
}
