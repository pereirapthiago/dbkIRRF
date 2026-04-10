<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Integration;

use DbkIrrf\Aplicacao\Servico\GeradorDbk;
use DbkIrrf\Aplicacao\Servico\LeitorDbk;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Formatador\FormatadorTexto;
use DbkIrrf\Infraestrutura\Validador\ValidadorRegistro;
use PHPUnit\Framework\TestCase;

/**
 * Testes de integracao: edge cases, FK entre registros, valores limite e caracteres especiais.
 */
final class EdgeCasesDbkTest extends TestCase
{
    private GeradorDbk $gerador;
    private LeitorDbk $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDbk();
        $this->leitor = new LeitorDbk();
    }

    // ========== 1. Declaracao Retificadora - posicoes de recibo ==========

    public function testRetificadoraDeveGravarReciboNasPosicoesCorretas(): void
    {
        $cpf = new Cpf('41653508000');
        $reciboAnterior = '1234567890';

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: $reciboAnterior,
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: $cpf,
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: $reciboAnterior,
        );
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Validar formato do arquivo gerado
        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);
        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));

        $linhas = explode("\r\n", rtrim($conteudo, "\r\n"));
        $linhaHeader = $linhas[0];
        $linhaReg16 = $linhas[1];

        // --- Header (IRPF) ---
        // reciboRetificadora: pos 124, tamanho 10 (deve conter o recibo)
        $reciboRetifHeader = substr($linhaHeader, 123, 10);
        $this->assertSame($reciboAnterior, $reciboRetifHeader, 'Header pos 124-133 deve conter o recibo na retificadora');

        // reciboOriginal: pos 204, tamanho 10 (deve estar vazio/espacos na retificadora)
        $reciboOrigHeader = substr($linhaHeader, 203, 10);
        $this->assertSame(str_repeat(' ', 10), $reciboOrigHeader, 'Header pos 204-213 deve estar vazio na retificadora');

        // --- Reg 16 (Dados Pessoais) ---
        // reciboRetificadora: pos 392, tamanho 10 (deve conter o recibo)
        $reciboRetifReg16 = substr($linhaReg16, 391, 10);
        $this->assertSame($reciboAnterior, $reciboRetifReg16, 'Reg16 pos 392-401 deve conter o recibo na retificadora');

        // reciboOriginal: pos 444, tamanho 10 (deve estar vazio/espacos na retificadora)
        $reciboOrigReg16 = substr($linhaReg16, 443, 10);
        $this->assertSame(str_repeat(' ', 10), $reciboOrigReg16, 'Reg16 pos 444-453 deve estar vazio na retificadora');
    }

    public function testRetificadoraRoundTripDevePreservarRecibo(): void
    {
        $cpf = new Cpf('41653508000');
        $reciboAnterior = '1234567890';

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: $reciboAnterior,
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: $cpf,
            tipoDeclaracao: TipoDeclaracao::RETIFICADORA,
            reciboDeclaracaoAnterior: $reciboAnterior,
        );
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);
        $lida = $this->leitor->ler($conteudo);

        // Header
        $this->assertSame(TipoDeclaracao::RETIFICADORA, $lida->header->tipoDeclaracao);
        $this->assertSame($reciboAnterior, $lida->header->reciboDeclaracaoAnterior);

        // Dados Pessoais
        $this->assertSame(TipoDeclaracao::RETIFICADORA, $lida->dadosPessoais->tipoDeclaracao);
        $this->assertSame($reciboAnterior, $lida->dadosPessoais->reciboDeclaracaoAnterior);
    }

    public function testOriginalDeveTerReciboNaPosicaoAlternativa(): void
    {
        $cpf = new Cpf('41653508000');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            reciboDeclaracaoAnterior: '9988776655',
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: $cpf,
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            reciboDeclaracaoAnterior: '9988776655',
        );
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);
        $linhas = explode("\r\n", rtrim($conteudo, "\r\n"));
        $linhaHeader = $linhas[0];
        $linhaReg16 = $linhas[1];

        // Header: reciboRetificadora (pos 124) deve estar vazio, reciboOriginal (pos 204) deve ter o recibo
        $reciboRetifHeader = substr($linhaHeader, 123, 10);
        $this->assertSame(str_repeat(' ', 10), $reciboRetifHeader, 'Header pos 124-133 deve estar vazio na original');

        $reciboOrigHeader = substr($linhaHeader, 203, 10);
        $this->assertSame('9988776655', $reciboOrigHeader, 'Header pos 204-213 deve conter o recibo na original');

        // Reg16: reciboRetificadora (pos 392) deve estar vazio, reciboOriginal (pos 444) deve ter o recibo
        $reciboRetifReg16 = substr($linhaReg16, 391, 10);
        $this->assertSame(str_repeat(' ', 10), $reciboRetifReg16, 'Reg16 pos 392-401 deve estar vazio na original');

        $reciboOrigReg16 = substr($linhaReg16, 443, 10);
        $this->assertSame('9988776655', $reciboOrigReg16, 'Reg16 pos 444-453 deve conter o recibo na original');
    }

    // ========== 2. FK Reg 37 (InvestExterior) <-> Reg 27 (BemDireito) ==========

    public function testFkReg37DeveReferenciarIdInternoDoReg27(): void
    {
        $cpf = new Cpf('41653508000');
        $idBem = '00001';

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf);
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);

        // Reg 27 - Bem/Direito (o idBem e gerenciado pelo Reg 37 via idBem)
        $declaracao->adicionarBemDireito(new RegistroBemDireitoDTO(
            cpf: $cpf,
            codigoItem: '99',
            descricao: 'FUNDO DE INVESTIMENTO EXTERIOR',
            valorAnterior: ValorMonetario::deCentavos(5000000),
            valorAtual: ValorMonetario::deCentavos(6000000),
        ));

        // Reg 37 - InvestExterior com idBem referenciando o Reg 27
        $declaracao->adicionarInvestimentoExterior(new RegistroInvestExteriorDTO(
            cpf: $cpf,
            idBem: $idBem,
            sequencialDetalhe: '00001',
            subTipo: SubTipoInvestimento::APLICACOES_FINANCEIRAS,
            rendimentoValor: ValorMonetario::deCentavos(100000),
            impostoDevido15: ValorMonetario::deCentavos(15000),
            grupoBem: '07',
            codigoItem: '99',
        ));

        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Validar formato
        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);
        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));

        // Ler de volta
        $lida = $this->leitor->ler($conteudo);

        $this->assertCount(1, $lida->obterBensDireitos());
        $this->assertCount(1, $lida->obterInvestimentosExterior());

        $investLido = $lida->obterInvestimentosExterior()[0];
        $this->assertSame($idBem, $investLido->idBem, 'Reg 37 idBem deve ser preservado no round-trip');
    }

    public function testFkReg37Reg27ComMultiplosBens(): void
    {
        $cpf = new Cpf('41653508000');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf);
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);

        // Dois bens
        $declaracao->adicionarBemDireito(new RegistroBemDireitoDTO(
            cpf: $cpf,
            descricao: 'FUNDO A',
        ));
        $declaracao->adicionarBemDireito(new RegistroBemDireitoDTO(
            cpf: $cpf,
            descricao: 'FUNDO B',
        ));

        // Dois investimentos exteriores com idBem distintos
        $declaracao->adicionarInvestimentoExterior(new RegistroInvestExteriorDTO(
            cpf: $cpf,
            idBem: '00001',
            rendimentoValor: ValorMonetario::deCentavos(50000),
        ));
        $declaracao->adicionarInvestimentoExterior(new RegistroInvestExteriorDTO(
            cpf: $cpf,
            idBem: '00002',
            rendimentoValor: ValorMonetario::deCentavos(75000),
        ));

        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);
        $lida = $this->leitor->ler($conteudo);

        $invests = $lida->obterInvestimentosExterior();

        $this->assertCount(2, $lida->obterBensDireitos());
        $this->assertCount(2, $invests);

        // Verificar que os idBem dos Reg 37 sao preservados no round-trip
        $this->assertSame('00001', $invests[0]->idBem);
        $this->assertSame('00002', $invests[1]->idBem);
    }

    // ========== 3. Valores monetarios limite ==========

    public function testValorMonetarioZeroDeveFormatarComTresDigitos(): void
    {
        $valor = new ValorMonetario(0);

        $this->assertSame(0, $valor->centavos);
        $this->assertSame('0000000000000', $valor->formatar(13));
        $this->assertSame(0.00, $valor->emReais());
    }

    public function testValorMonetarioMaximo13DigitosDeveFormatar(): void
    {
        $valor = new ValorMonetario(9999999999999);

        $this->assertSame(9999999999999, $valor->centavos);
        $this->assertSame('9999999999999', $valor->formatar(13));
        $this->assertSame(99999999999.99, $valor->emReais());
    }

    public function testValorMonetarioComCentavos(): void
    {
        // 150099 centavos = R$ 1.500,99
        $valor = new ValorMonetario(150099);

        $this->assertSame(150099, $valor->centavos);
        $this->assertSame(1500.99, $valor->emReais());
        $this->assertSame('0000000150099', $valor->formatar(13));
    }

    public function testValorMonetarioNegativoDeveLancarExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('nao pode ser negativo');

        new ValorMonetario(-1);
    }

    public function testValorMonetarioDeReaisComArredondamento(): void
    {
        // R$ 1.500,99 -> 150099 centavos
        $valor = ValorMonetario::deReais(1500.99);
        $this->assertSame(150099, $valor->centavos);

        // R$ 0,01 -> 1 centavo
        $valorMin = ValorMonetario::deReais(0.01);
        $this->assertSame(1, $valorMin->centavos);
    }

    public function testValorMonetarioDeStringComZerosAEsquerda(): void
    {
        $valor = ValorMonetario::deString('0000000150099');
        $this->assertSame(150099, $valor->centavos);

        $valorZero = ValorMonetario::deString('0000000000000');
        $this->assertSame(0, $valorZero->centavos);
    }

    public function testValorMonetarioRoundTripViaArquivo(): void
    {
        $cpf = new Cpf('41653508000');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            impostoAPagar: ValorMonetario::deCentavos(150099),
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);
        $lida = $this->leitor->ler($conteudo);

        $this->assertSame(150099, $lida->header->impostoAPagar->centavos);
        $this->assertSame(1500.99, $lida->header->impostoAPagar->emReais());
    }

    public function testValorMonetarioZeroRoundTripViaArquivo(): void
    {
        $cpf = new Cpf('41653508000');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            impostoAPagar: ValorMonetario::deCentavos(0),
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);
        $lida = $this->leitor->ler($conteudo);

        $this->assertSame(0, $lida->header->impostoAPagar->centavos);
    }

    // ========== 4. Campos texto com caracteres especiais ==========

    public function testFormatadorTextoDeveRemoverAcentuacao(): void
    {
        $formatador = new FormatadorTexto();

        // Acentos devem ser removidos/convertidos
        $resultado = $formatador->formatar('JOSÉ ANTÔNIO DA SILVA ARAÚJO', 60);
        $this->assertStringNotContainsString('É', $resultado);
        $this->assertStringNotContainsString('Ô', $resultado);
        $this->assertStringNotContainsString('Ú', $resultado);
        $this->assertStringContainsString('JOSE ANTONIO DA SILVA ARAUJO', $resultado);
    }

    public function testFormatadorTextoDeveRemoverAcentuacaoMinuscula(): void
    {
        $formatador = new FormatadorTexto();

        $resultado = $formatador->formatar('josé antônio', 60);
        $this->assertStringContainsString('JOSE ANTONIO', $resultado);
    }

    public function testFormatadorTextoDeveTruncarQuandoExcedeTamanho(): void
    {
        $formatador = new FormatadorTexto();

        $textoLongo = str_repeat('A', 100);
        $resultado = $formatador->formatar($textoLongo, 60);

        $this->assertSame(60, strlen($resultado));
        $this->assertSame(str_repeat('A', 60), $resultado);
    }

    public function testFormatadorTextoDevePreencherComEspacosQuandoVazio(): void
    {
        $formatador = new FormatadorTexto();

        $resultado = $formatador->formatar('', 60);

        $this->assertSame(60, strlen($resultado));
        $this->assertSame(str_repeat(' ', 60), $resultado);
    }

    public function testFormatadorTextoDevePreencherComEspacosQuandoMenor(): void
    {
        $formatador = new FormatadorTexto();

        $resultado = $formatador->formatar('ABC', 10);

        $this->assertSame(10, strlen($resultado));
        $this->assertSame('ABC       ', $resultado);
    }

    public function testCedilhaDeveSerConvertidaParaC(): void
    {
        $formatador = new FormatadorTexto();

        $resultado = $formatador->formatar('CONCEIÇÃO', 20);
        $this->assertStringContainsString('CONCEICAO', $resultado);
    }

    public function testTextoComAcentuacaoRoundTripViaArquivo(): void
    {
        $cpf = new Cpf('41653508000');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            nome: 'JOSÉ ANTÔNIO DA CONCEIÇÃO',
            cidade: 'PETRÓPOLIS',
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(
            cpf: $cpf,
            nome: 'JOSÉ ANTÔNIO DA CONCEIÇÃO',
            municipio: 'PETRÓPOLIS',
        );
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Validar formato
        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);
        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));

        $lida = $this->leitor->ler($conteudo);

        // Acentos devem ter sido removidos pelo FormatadorTexto
        $this->assertSame('JOSE ANTONIO DA CONCEICAO', $lida->header->nome);
        $this->assertSame('PETROPOLIS', $lida->header->cidade);
        $this->assertSame('JOSE ANTONIO DA CONCEICAO', $lida->dadosPessoais->nome);
        $this->assertSame('PETROPOLIS', $lida->dadosPessoais->municipio);
    }

    public function testTextoTruncadoRoundTripViaArquivo(): void
    {
        $cpf = new Cpf('41653508000');
        $nomeLongo = str_repeat('A', 100); // excede 60 chars do campo nome

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            nome: $nomeLongo,
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);
        $lida = $this->leitor->ler($conteudo);

        // Nome deve ter sido truncado para 60 chars
        $this->assertSame(60, strlen($lida->header->nome));
        $this->assertSame(str_repeat('A', 60), $lida->header->nome);
    }

    public function testTextoVazioRoundTripViaArquivo(): void
    {
        $cpf = new Cpf('41653508000');

        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            nome: '',
            cidade: '',
        );
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Validar que o arquivo gerado e valido mesmo com campos vazios
        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);
        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));

        $lida = $this->leitor->ler($conteudo);

        // Campos vazios voltam como string vazia apos rtrim (extrairTexto)
        $this->assertSame('', $lida->header->nome);
        $this->assertSame('', $lida->header->cidade);
    }

    // ========== 5. Arquivo com tipos desconhecidos ==========

    public function testLeitorDeveIgnorarLinhaComTipo19(): void
    {
        $cpf = new Cpf('41653508000');
        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf);
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Inserir uma linha falsa de tipo '19' (nao existe no enum TipoRegistro)
        // Usar 346 chars para simular um registro qualquer
        $linhaFake19 = '19' . str_repeat('0', 344);

        $linhas = explode("\r\n", $conteudo);
        // Inserir apos o header e dados pessoais (pos 2)
        array_splice($linhas, 2, 0, [$linhaFake19]);
        $conteudoComFake = implode("\r\n", $linhas);

        // O leitor nao deve lancar excecao
        $lida = $this->leitor->ler($conteudoComFake);

        $this->assertNotNull($lida->header);
        $this->assertNotNull($lida->dadosPessoais);
        $this->assertNotNull($lida->trailer);
    }

    public function testLeitorDeveIgnorarMultiplasLinhasDesconhecidas(): void
    {
        $cpf = new Cpf('41653508000');
        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf);
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Tipos excluidos do escopo
        $linhaFake19 = '19' . str_repeat('0', 344);
        $linhaFake20 = '20' . str_repeat('0', 924);
        $linhaFake99 = '99' . str_repeat('X', 200);

        $linhas = explode("\r\n", $conteudo);
        array_splice($linhas, 2, 0, [$linhaFake19, $linhaFake20, $linhaFake99]);
        $conteudoComFakes = implode("\r\n", $linhas);

        $lida = $this->leitor->ler($conteudoComFakes);

        $this->assertNotNull($lida->header, 'Header deve ser lido mesmo com linhas desconhecidas no meio');
        $this->assertNotNull($lida->dadosPessoais, 'Dados pessoais devem ser lidos mesmo com linhas desconhecidas');
        $this->assertNotNull($lida->trailer, 'Trailer deve ser lido mesmo com linhas desconhecidas');
        $this->assertSame('41653508000', $lida->header->cpf->valor);
    }

    public function testValidadorDeveIgnorarTiposDesconhecidos(): void
    {
        $cpf = new Cpf('41653508000');
        $declaracao = new DeclaracaoDTO();
        $declaracao->header = new RegistroHeaderDTO(cpf: $cpf);
        $declaracao->dadosPessoais = new RegistroDadosPessoaisDTO(cpf: $cpf);
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf);

        $conteudo = $this->gerador->gerar($declaracao);

        // Inserir tipo desconhecido - nao deve causar erro de validacao
        $linhaFake = '19' . str_repeat('0', 344);
        $linhas = explode("\r\n", $conteudo);
        array_splice($linhas, 2, 0, [$linhaFake]);
        $conteudoComFake = implode("\r\n", $linhas);

        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudoComFake);

        // O validador ignora tipos desconhecidos (retorna null em identificarPorLinha)
        // entao as linhas conhecidas (header, reg16, trailer) devem continuar validas
        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));
    }
}
