<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\ValorObjeto;

use DbkIrrf\Dominio\ValorObjeto\Checksum;
use PHPUnit\Framework\TestCase;

final class ChecksumTest extends TestCase
{
    public function testDeveCriarChecksumValido(): void
    {
        $cs = new Checksum('3267140398');
        $this->assertSame('3267140398', $cs->valor);
        $this->assertSame('3267140398', (string) $cs);
    }

    public function testPlaceholderDeveRetornarZeros(): void
    {
        $cs = Checksum::placeholder();
        $this->assertSame('0000000000', $cs->valor);
    }

    public function testDeveExtrairDeLinhaUltimos10Chars(): void
    {
        $linha = str_repeat('X', 160) . '1234567890';
        $cs = Checksum::deLinha($linha);
        $this->assertSame('1234567890', $cs->valor);
    }

    public function testDeveLancarExcecaoParaTamanhoIncorreto(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Checksum('12345');
    }
}
