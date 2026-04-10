<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\ValorObjeto;

use DbkIrrf\Dominio\ValorObjeto\Cpf;
use PHPUnit\Framework\TestCase;

final class CpfTest extends TestCase
{
    public function testDeveCriarCpfComDigitosValidos(): void
    {
        $cpf = new Cpf('41653508000');

        $this->assertSame('41653508000', $cpf->valor);
        $this->assertSame('41653508000', (string) $cpf);
    }

    public function testDeveRemoverCaracteresNaoNumericos(): void
    {
        $cpf = new Cpf('416.535.080-00');

        $this->assertSame('41653508000', $cpf->valor);
    }

    public function testDeveLancarExcecaoParaCpfComMenosDe11Digitos(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Cpf('1234567890');
    }

    public function testDeveLancarExcecaoParaCpfComMaisDe11Digitos(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Cpf('123456789012');
    }

    public function testDeveCompararIgualdade(): void
    {
        $cpf1 = new Cpf('41653508000');
        $cpf2 = new Cpf('41653508000');
        $cpf3 = new Cpf('13480293077');

        $this->assertTrue($cpf1->igual($cpf2));
        $this->assertFalse($cpf1->igual($cpf3));
    }
}
