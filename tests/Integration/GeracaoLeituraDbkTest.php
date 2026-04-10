<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Integration;

use DbkIrrf\Aplicacao\Fabrica\FabricaDeclaracao;
use DbkIrrf\Aplicacao\Servico\GeradorDbk;
use DbkIrrf\Aplicacao\Servico\LeitorDbk;
use DbkIrrf\Aplicacao\Servico\NomeadorArquivo;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\DTO\RegistroDeducaoLegalDTO;
use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroImpostoPagoDTO;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento84DTO;
use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosMensaisDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\DTO\RegistroTrailerDTO;
use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Validador\ValidadorRegistro;
use PHPUnit\Framework\TestCase;

final class GeracaoLeituraDbkTest extends TestCase
{
    private GeradorDbk $gerador;
    private LeitorDbk $leitor;

    protected function setUp(): void
    {
        $this->gerador = new GeradorDbk();
        $this->leitor = new LeitorDbk();
    }

    public function testDeveGerarElerDeclaracaoCompletaRoundTrip(): void
    {
        $declaracao = $this->criarDeclaracaoCompleta();

        // Gerar
        $conteudo = $this->gerador->gerar($declaracao);
        $this->assertNotEmpty($conteudo);

        // Validar formato
        $validador = new ValidadorRegistro();
        $resultado = $validador->validarArquivo($conteudo);
        $this->assertTrue($resultado->valido, implode('; ', $resultado->erros));

        // Ler
        $lida = $this->leitor->ler($conteudo);

        // Comparar Header
        $this->assertNotNull($lida->header);
        $this->assertSame('41653508000', $lida->header->cpf->valor);
        $this->assertSame(2026, $lida->header->anoExercicio);
        $this->assertSame(2025, $lida->header->anoCalendario);
        $this->assertSame(TipoDeclaracao::ORIGINAL, $lida->header->tipoDeclaracao);
        $this->assertSame('JORGE LUCAS DA SILVA MONTANO', $lida->header->nome);
        $this->assertSame(UnidadeFederativa::RJ, $lida->header->uf);
        $this->assertSame(EstadoCivil::SOLTEIRO, $lida->header->estadoCivil);
        $this->assertSame(1480109, $lida->header->impostoAPagar->centavos);

        // Comparar Dados Pessoais
        $this->assertNotNull($lida->dadosPessoais);
        $this->assertSame('AV KOELER', $lida->dadosPessoais->logradouro);
        $this->assertSame('PETROPOLIS', $lida->dadosPessoais->municipio);

        // Comparar Rendimentos PJ
        $this->assertCount(1, $lida->obterRendimentosPJ());
        $this->assertSame(180000.00, $lida->obterRendimentosPJ()[0]->rendimentosRecebidos->emReais());

        // Comparar Rendimentos Mensais (12 meses)
        $this->assertCount(12, $lida->obterRendimentosMensais());
        $this->assertSame(1, $lida->obterRendimentosMensais()[0]->mesReferencia);
        $this->assertSame(12, $lida->obterRendimentosMensais()[11]->mesReferencia);

        // Comparar Imposto Pago
        $this->assertCount(1, $lida->obterImpostosPagos());
        $this->assertSame('0001', $lida->obterImpostosPagos()[0]->codigo);

        // Comparar Deducoes (3)
        $this->assertCount(3, $lida->obterDeducoesLegais());

        // Comparar Dependente
        $this->assertCount(1, $lida->obterDependentes());
        $this->assertSame('RYAN SILVA MONTANO', $lida->obterDependentes()[0]->nomeDependente);

        // Comparar Pagamento
        $this->assertCount(1, $lida->obterPagamentos());
        $this->assertSame(5000.00, $lida->obterPagamentos()[0]->valorPago->emReais());

        // Comparar Bens
        $this->assertCount(1, $lida->obterBensDireitos());
        $this->assertNotNull($lida->obterBensDireitos()[0]);

        // Comparar Divida
        $this->assertCount(1, $lida->obterDividas());
        $this->assertSame(150000.00, $lida->obterDividas()[0]->saldoAnterior->emReais());

        // Comparar RRA (Reg 45)
        $this->assertCount(1, $lida->obterRras());

        // Comparar Rendimento Isento 84
        $this->assertCount(1, $lida->obterRendimentosIsentos84());
        $this->assertSame(5000.00, $lida->obterRendimentosIsentos84()[0]->valorRendimentoIsento->emReais());

        // Comparar Trib Exclusiva
        $this->assertCount(1, $lida->obterTribExclusivas());
        $this->assertSame(10000.00, $lida->obterTribExclusivas()[0]->valorRendimento->emReais());

        // Comparar Trailer
        $this->assertNotNull($lida->trailer);
        $this->assertSame('41653508000', $lida->trailer->cpf->valor);
    }

    public function testDeveGerarNomeArquivoCorreto(): void
    {
        $nomeador = new NomeadorArquivo();

        $nome = $nomeador->gerar(
            new Cpf('41653508000'),
            2026,
            2025,
            TipoDeclaracao::ORIGINAL,
        );

        $this->assertSame('41653508000-IRPF-A-2026-2025-ORIGI.DBK', $nome);

        $nomeRetif = $nomeador->gerar(
            new Cpf('41653508000'),
            2026,
            2025,
            TipoDeclaracao::RETIFICADORA,
        );

        $this->assertSame('41653508000-IRPF-A-2026-2025-RETIF.DBK', $nomeRetif);
    }

    public function testDeveGerarNomeArquivoViaDeclaracao(): void
    {
        $declaracao = $this->criarDeclaracaoCompleta();
        $nomeador = new NomeadorArquivo();

        $this->assertSame(
            '41653508000-IRPF-A-2026-2025-ORIGI.DBK',
            $nomeador->gerarDeDeclaracao($declaracao),
        );
    }

    public function testFabricaDeclaracaoDeveCriarDeclaracaoBase(): void
    {
        $fabrica = new FabricaDeclaracao();
        $declaracao = $fabrica->criar(new Cpf('41653508000'));

        $this->assertNotNull($declaracao->header);
        $this->assertNotNull($declaracao->dadosPessoais);
        $this->assertNotNull($declaracao->trailer);
        $this->assertSame('41653508000', $declaracao->header->cpf->valor);
        $this->assertSame(2026, $declaracao->header->anoExercicio);
    }

    public function testDeveContarLinhasCorretas(): void
    {
        $declaracao = $this->criarDeclaracaoCompleta();
        $conteudo = $this->gerador->gerar($declaracao);
        $linhas = explode("\r\n", rtrim($conteudo, "\r\n"));

        // Header + DadosPessoais + 1xReg21 + 12xReg22 + 1xReg23 + 3xReg24
        // + 1xReg25 + 1xReg26 + 1xReg27 + 1xReg28 + 1xReg45 + 1xReg84
        // + 1xReg88 + Trailer = 27 linhas
        $this->assertSame(27, count($linhas));
    }

    private function criarDeclaracaoCompleta(): DeclaracaoDTO
    {
        $cpf = new Cpf('41653508000');
        $cnpj = new Cnpj('27865757000102');
        $declaracao = new DeclaracaoDTO();

        $declaracao->header = new RegistroHeaderDTO(
            cpf: $cpf,
            anoExercicio: 2026,
            anoCalendario: 2025,
            tipoDeclaracao: TipoDeclaracao::ORIGINAL,
            codigoNaturezaOcupacao: '1100',
            nome: 'JORGE LUCAS DA SILVA MONTANO',
            uf: UnidadeFederativa::RJ,
            dataNascimento: Data::deDateTime(new \DateTime('2000-10-10')),
            estadoCivil: EstadoCivil::SOLTEIRO,
            codigoMunicipioIbge: '5877',
            cep: '25845060',
            cidade: 'PETROPOLIS',
            impostoAPagar: ValorMonetario::deCentavos(1480109),
            cnpjFontePrincipal: $cnpj,
            cpfDependenteConjuge: new Cpf('13480293077'),
            dataNascimentoDependente: Data::deDateTime(new \DateTime('2021-03-15')),
        );

        $declaracao->dadosPessoais = new \DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO(
            cpf: $cpf,
            nome: 'JORGE LUCAS DA SILVA MONTANO',
            tipoLogradouro: 'RUA',
            logradouro: 'AV KOELER',
            numero: '260',
            complemento: 'CASA',
            bairro: 'CENTRO',
            cep: '25845060',
            codigoMunicipioIbge: '5877',
            municipio: 'PETROPOLIS',
            uf: UnidadeFederativa::RJ,
            email: 'JORGEMONTANO@GMAIL.COM',
            dddCelular: '24',
            celular: '999999999',
            dataNascimento: Data::deDateTime(new \DateTime('2000-10-10')),
        );

        // Reg 21
        $declaracao->adicionarRendimentoPJ(new RegistroRendimentosPJDTO(
            cpf: $cpf,
            cnpjFontePagadora: $cnpj,
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            rendimentosRecebidos: ValorMonetario::deCentavos(18000000),
            contribPrevidenciaria: ValorMonetario::deCentavos(1200000),
            decimoTerceiroSalario: ValorMonetario::deCentavos(1200000),
            impostoRetidoFonte: ValorMonetario::deCentavos(5000000),
            irrfDecimoTerceiro: ValorMonetario::deCentavos(100000),
        ));

        // Reg 22 - 12 meses
        for ($mes = 1; $mes <= 12; $mes++) {
            $declaracao->adicionarRendimentoMensal(new RegistroRendimentosMensaisDTO(
                cpf: $cpf,
                mesReferencia: $mes,
                exterior: ValorMonetario::deCentavos(1500000),
                totalRendimentosMes: ValorMonetario::deCentavos(1500000),
                darfPago: ValorMonetario::deCentavos(150000),
            ));
        }

        // Reg 23
        $declaracao->adicionarImpostoPago(new RegistroImpostoPagoDTO(
            cpf: $cpf, codigo: '0001', valor: ValorMonetario::deCentavos(500000),
        ));

        // Reg 24
        $declaracao->adicionarDeducaoLegal(new RegistroDeducaoLegalDTO(
            cpf: $cpf, codigoDeducao: '0001', valor: ValorMonetario::deCentavos(1200000),
        ));
        $declaracao->adicionarDeducaoLegal(new RegistroDeducaoLegalDTO(
            cpf: $cpf, codigoDeducao: '0006', valor: ValorMonetario::deCentavos(1000000),
        ));
        $declaracao->adicionarDeducaoLegal(new RegistroDeducaoLegalDTO(
            cpf: $cpf, codigoDeducao: '0007', valor: ValorMonetario::deCentavos(100000),
        ));

        // Reg 25
        $declaracao->adicionarDependente(new RegistroDependenteDTO(
            cpf: $cpf,
            tipoDependente: new CodigoDependente(CodigoDependente::FILHO_ATE_21),
            nomeDependente: 'RYAN SILVA MONTANO',
            dataNascimento: Data::deDateTime(new \DateTime('2021-03-15')),
            cpfDependente: new Cpf('13480293077'),
        ));

        // Reg 26
        $declaracao->adicionarPagamento(new RegistroPagamentoDTO(
            cpf: $cpf,
            cpfCnpjBeneficiario: '66313835018',
            nomeBeneficiario: 'EZEQUIEL LUIS MENDONCA',
            valorPago: ValorMonetario::deCentavos(500000),
            parcelaNaoDedutivel: ValorMonetario::deCentavos(111100),
            descricao: 'PAGAMENTO DE MEDICO',
        ));

        // Reg 27
        $declaracao->adicionarBemDireito(new RegistroBemDireitoDTO(
            cpf: $cpf,
            codigoItem: '11',
            descricao: 'APARTAMENTO PETROPOLIS',
            valorAnterior: ValorMonetario::deCentavos(10000000),
            valorAtual: ValorMonetario::deCentavos(20000000),
        ));

        // Reg 28
        $declaracao->adicionarDivida(new RegistroDividaDTO(
            cpf: $cpf,
            codigoDivida: '11',
            descricao: 'EMPRESTIMO PARA APARTAMENTO',
            saldoAnterior: ValorMonetario::deCentavos(15000000),
            saldoAtual: ValorMonetario::deCentavos(20000000),
            valorPagoAno: ValorMonetario::deCentavos(2000000),
        ));

        // Reg 45 - RRA
        $rendRra = ValorMonetario::deCentavos(85465); // R$ 854,65
        $declaracao->adicionarRra(new RegistroRraDTO(
            cpf: $cpf,
            cnpjFontePagadora: $cnpj,
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            contribPrevidenciaria: ValorMonetario::deCentavos(33365),
            impostoRetidoFonte: ValorMonetario::deCentavos(2164),
            mesRecebimentoRRA: '03',
            numMesesRRA: '3',
            rendimentosRRA: $rendRra,
            rendimentosRRACopia: $rendRra,
        ));

        // Reg 84
        $declaracao->adicionarRendimentoIsento84(new RegistroRendimentoIsento84DTO(
            cpf: $cpf,
            cpfBeneficiario: $cpf,
            cnpjFontePagadora: $cnpj,
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            valorRendimentoIsento: ValorMonetario::deCentavos(500000),
        ));

        // Reg 88
        $declaracao->adicionarTribExclusiva(new RegistroTribExclusivaDTO(
            cpf: $cpf,
            cpfBeneficiario: $cpf,
            cnpjFontePagadora: $cnpj,
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            valorRendimento: ValorMonetario::deCentavos(1000000),
        ));

        // Trailer
        $declaracao->trailer = new RegistroTrailerDTO(cpf: $cpf, totalRegistros: 25);

        return $declaracao;
    }
}
