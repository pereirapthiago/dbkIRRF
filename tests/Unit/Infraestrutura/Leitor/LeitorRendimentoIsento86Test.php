<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Leitor;

use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento86DTO;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentoIsento86;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentoIsento86;
use PHPUnit\Framework\TestCase;

final class LeitorRendimentoIsento86Test extends TestCase
{
    private GeradorRendimentoIsento86 $gerador;
    private LeitorRendimentoIsento86 $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorRendimentoIsento86();
        $this->leitor = new LeitorRendimentoIsento86();
    }

    public function testDeveRealizarRoundTripCompleto(): void
    {
        $original = new RegistroRendimentoIsento86DTO(
            cpf: new Cpf('41653508000'),
            tipoBeneficiario: TipoBeneficiario::TITULAR,
            cpfBeneficiario: new Cpf('41653508000'),
            codigoTipoRendimento: '0026',
            cnpjFontePagadora: '40278681000179',
            nomeFontePagadora: 'TRANSOCEAN BRASIL LTDA',
            valorRendimentoIsento: new ValorMonetario(583523),
            descricaoLivre: 'RENDIMENTO ISENTO DE ALUGUEL NO EXTERIOR.',
        );

        $linha = $this->gerador->gerar($original);
        $this->assertSame(191, strlen($linha));

        /** @var RegistroRendimentoIsento86DTO $lido */
        $lido = $this->leitor->ler($linha);

        $this->assertSame('41653508000', $lido->cpf->valor);
        $this->assertSame(TipoBeneficiario::TITULAR, $lido->tipoBeneficiario);
        $this->assertSame('41653508000', $lido->cpfBeneficiario->valor);
        $this->assertSame('0026', $lido->codigoTipoRendimento);
        $this->assertSame('40278681000179', $lido->cnpjFontePagadora);
        $this->assertSame('TRANSOCEAN BRASIL LTDA', $lido->nomeFontePagadora);
        $this->assertSame(583523, $lido->valorRendimentoIsento->centavos);
        $this->assertSame('RENDIMENTO ISENTO DE ALUGUEL NO EXTERIOR.', $lido->descricaoLivre);
    }

    public function testDeveSuportarTipoRendimentoIsentoOutros(): void
    {
        $this->assertSame(TipoRegistro::RENDIMENTO_ISENTO_OUTROS, $this->leitor->suportaTipo());
    }

    public function testDeveTerTamanho191(): void
    {
        $dto = new RegistroRendimentoIsento86DTO(
            cpf: new Cpf('12345678901'),
            nomeFontePagadora: 'EMPRESA TESTE',
            valorRendimentoIsento: new ValorMonetario(100000),
            descricaoLivre: 'Descricao de teste',
        );

        $linha = $this->gerador->gerar($dto);
        $this->assertSame(191, strlen($linha));
    }
}
