<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Integration;

use DbkIrrf\Aplicacao\Servico\GeradorDbk;
use DbkIrrf\Aplicacao\Servico\LeitorDbk;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Validador\ValidadorRegistro;
use PHPUnit\Framework\TestCase;

/**
 * Bug Hunter - Fase 5: Validacao de integridade e cenarios extremos.
 */
final class ValidacaoDbkTest extends TestCase
{
    // ========== 5.5 Arquivo com linhas faltantes ==========

    public function testDeveIgnorarLinhasVaziasNaLeitura(): void
    {
        $gerador = new GeradorDbk();
        $leitor = new LeitorDbk();

        $declaracao = $this->criarDeclaracaoMinima();
        $conteudo = $gerador->gerar($declaracao);

        // Adiciona linhas vazias extras
        $conteudo = "\r\n\r\n" . $conteudo . "\r\n\r\n";
        $lida = $leitor->ler($conteudo);

        $this->assertNotNull($lida->header);
        $this->assertNotNull($lida->trailer);
    }

    // ========== 5.6 Tamanho de linha incorreto ==========

    public function testValidadorDeveDetectarLinhaComTamanhoIncorreto(): void
    {
        $validador = new ValidadorRegistro();

        // Linha IRPF com tamanho errado
        $linhaErrada = 'IRPF' . str_repeat(' ', 100);
        $resultado = $validador->validarArquivo($linhaErrada);

        $this->assertFalse($resultado->valido);
        $this->assertNotEmpty($resultado->erros);
    }

    public function testValidadorDeveAprovarArquivoValido(): void
    {
        $gerador = new GeradorDbk();
        $declaracao = $this->criarDeclaracaoMinima();
        $conteudo = $gerador->gerar($declaracao);

        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);

        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));
    }

    // ========== 5.7 Declaracao retificadora ==========

    public function testDeclaracaoRetificadoraDeveInverterPosicaoRecibo(): void
    {
        $gerador = new GeradorDbk();
        $leitor = new LeitorDbk();

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: '9876543210',
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: new Cpf('41653508000'),
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: '9876543210',
        );
        $declaracao->trailer = new RegistroTrailerDTO(cpf: new Cpf('41653508000'));

        $conteudo = $gerador->gerar($declaracao);
        $lida = $leitor->ler($conteudo);

        // Header: recibo deve estar na posicao correta
        $this->assertSame(TipoDeclaracao::RETIFICADORA, $lida->header->tipoDeclaracao);
        $this->assertSame('9876543210', $lida->header->reciboDeclaracaoAnterior);

        // DadosPessoais: flag retificadora e recibo
        $this->assertSame(TipoDeclaracao::RETIFICADORA, $lida->dadosPessoais->tipoDeclaracao);
        $this->assertSame('9876543210', $lida->dadosPessoais->reciboDeclaracaoAnterior);
    }

    // ========== 5.8 Integridade: tamanho de cada linha gerada ==========

    public function testTodasLinhasGeradasDevemTerTamanhoCorreto(): void
    {
        $gerador = new GeradorDbk();
        $declaracao = $this->criarDeclaracaoCompleta();
        $conteudo = $gerador->gerar($declaracao);

        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);

        $this->assertTrue(
            $resultado->valido,
            "Linhas com tamanho incorreto: " . implode('; ', $resultado->erros)
        );
    }

    // ========== 5.5 Leitor deve ignorar tipos desconhecidos (Reg 19/20) ==========

    public function testLeitorDeveIgnorarLinhasDeTipoDesconhecido(): void
    {
        $leitor = new LeitorDbk();

        // Simula um arquivo com linhas de tipo 19 e 20 (fora do escopo)
        $gerador = new GeradorDbk();
        $declaracao = $this->criarDeclaracaoMinima();
        $conteudo = $gerador->gerar($declaracao);

        // Insere linhas fake de Reg 19 (346 chars) e Reg 20 (926 chars) no meio
        $linhaFake19 = '19' . str_repeat('0', 344);
        $linhaFake20 = '20' . str_repeat('0', 924);

        $linhas = explode("\r\n", $conteudo);
        array_splice($linhas, 2, 0, [$linhaFake19, $linhaFake20]);
        $conteudoComExtras = implode("\r\n", $linhas);

        $lida = $leitor->ler($conteudoComExtras);

        $this->assertNotNull($lida->header);
        $this->assertNotNull($lida->dadosPessoais);
        $this->assertNotNull($lida->trailer);
    }

    // ========== Helpers ==========

    private function criarDeclaracaoMinima(): DeclaracaoDTO
    {
        $cpf = new Cpf('41653508000');
        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf);
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf, totalRegistros: 2);
        return $declaracao;
    }

    private function criarDeclaracaoCompleta(): DeclaracaoDTO
    {
        $cpf = new Cpf('41653508000');
        $cnpj = new Cnpj('27865757000102');
        $declaracao = $this->criarDeclaracaoMinima();

        $declaracao->adicionarRendimentoPJ(new RegistroRendimentosPJDTO(
            cpf: $cpf,
            cnpjFontePagadora: $cnpj,
            nomeFontePagadora: 'TESTE',
            rendimentosRecebidos: ValorMonetario::deCentavos(18000000),
        ));

        for ($mes = 1; $mes <= 12; $mes++) {
            $declaracao->adicionarRendimentoMensal(new RegistroRendimentosMensaisDTO(
                cpf: $cpf,
                mesReferencia: $mes,
                exterior: ValorMonetario::deCentavos(1500000),
                totalRendimentosMes: ValorMonetario::deCentavos(1500000),
            ));
        }

        return $declaracao;
    }
}
