<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Validador;

use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Infraestrutura\Validador\ValidadorRegistro;
use PHPUnit\Framework\TestCase;

final class ValidadorRegistroTest extends TestCase
{
    private ValidadorRegistro $validador;

    protected function setUp(): void
    {
        $this->validador = new ValidadorRegistro();
    }

    private function criarDeclaracaoValida(): DeclaracaoDTO
    {
        $cpf = new Cpf('12345678901');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf, nome: 'FULANO DE TAL');
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        return $declaracao;
    }

    public function testDeveValidarDeclaracaoCompleta(): void
    {
        $declaracao = $this->criarDeclaracaoValida();

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertTrue($resultado->valido);
        $this->assertEmpty($resultado->erros);
    }

    public function testDeveDetectarHeaderFaltando(): void
    {
        $cpf = new Cpf('12345678901');

        $declaracao = new DeclaracaoDTO();
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertFalse($resultado->valido);
        $this->assertNotEmpty($resultado->erros);
        $this->assertStringContainsString('Header', $resultado->erros[0]);
    }

    public function testDeveDetectarDadosPessoaisFaltando(): void
    {
        $cpf = new Cpf('12345678901');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf, nome: 'FULANO DE TAL');
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertFalse($resultado->valido);
        $this->assertNotEmpty($resultado->erros);
        $this->assertStringContainsString('Dados pessoais', $resultado->erros[0]);
    }

    public function testDeveDetectarTrailerFaltando(): void
    {
        $cpf = new Cpf('12345678901');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf, nome: 'FULANO DE TAL');
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertFalse($resultado->valido);
        $this->assertNotEmpty($resultado->erros);
        $this->assertStringContainsString('Trailer', $resultado->erros[0]);
    }

    public function testDeveDetectarCpfInconsistente(): void
    {
        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: new Cpf('12345678901'),
            nome: 'FULANO DE TAL',
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: new Cpf('98765432100'),
        );
        $declaracao->trailer = new RegistroTrailerDTO(
            cpf: new Cpf('12345678901'),
        );

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertFalse($resultado->valido);

        $encontrouErroCpf = false;
        foreach ($resultado->erros as $erro) {
            if (str_contains($erro, 'CPF do header')) {
                $encontrouErroCpf = true;
                break;
            }
        }
        $this->assertTrue($encontrouErroCpf, 'Deveria conter erro de CPF inconsistente');
    }

    public function testDeveDetectarNomeVazio(): void
    {
        $cpf = new Cpf('12345678901');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf, nome: '');
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertFalse($resultado->valido);

        $encontrouErroNome = false;
        foreach ($resultado->erros as $erro) {
            if (str_contains($erro, 'Nome do contribuinte')) {
                $encontrouErroNome = true;
                break;
            }
        }
        $this->assertTrue($encontrouErroNome, 'Deveria conter erro de nome vazio');
    }

    public function testDeveAceitarReg37ComReg27(): void
    {
        $cpf = new Cpf('12345678901');

        $declaracao = $this->criarDeclaracaoValida();

        $declaracao->adicionarBemDireito(
            new RegistroBemDireitoDTO(cpf: $cpf),
        );

        $declaracao->adicionarInvestimentoExterior(
            new RegistroInvestExteriorDTO(cpf: $cpf, idBem: '00001'),
        );

        $resultado = $this->validador->validarDeclaracao($declaracao);

        $this->assertTrue($resultado->valido);
        $this->assertEmpty($resultado->erros);
    }
}
