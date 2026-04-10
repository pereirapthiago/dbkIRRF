<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Dominio\ValorObjeto;

use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use PHPUnit\Framework\TestCase;

final class CnpjTest extends TestCase
{
    public function testDeveCriarCnpjValido(): void
    {
        $cnpj = new Cnpj('27865757000102');
        $this->assertSame('27865757000102', $cnpj->valor);
        $this->assertSame('27865757000102', (string) $cnpj);
    }

    public function testDeveRemoverFormatacao(): void
    {
        $cnpj = new Cnpj('27.865.757/0001-02');
        $this->assertSame('27865757000102', $cnpj->valor);
    }

    public function testDeveLancarExcecaoParaCnpjCurto(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cnpj('1234567890123');
    }

    public function testDeveLancarExcecaoParaCnpjLongo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cnpj('123456789012345');
    }

    public function testDeveCompararIgualdade(): void
    {
        $c1 = new Cnpj('27865757000102');
        $c2 = new Cnpj('27865757000102');
        $c3 = new Cnpj('98987984564066');
        $this->assertTrue($c1->igual($c2));
        $this->assertFalse($c1->igual($c3));
    }
}
