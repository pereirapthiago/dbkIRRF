<?php

declare(strict_types=1);

namespace DbkIrrf\Tests\Unit\Infraestrutura\Gerador;

use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\DTO\RegistroDependenteDTO;
use DbkIrrf\Dominio\DTO\RegistroDividaDTO;
use DbkIrrf\Dominio\DTO\RegistroInvestExteriorDTO;
use DbkIrrf\Dominio\DTO\RegistroPagamentoDTO;
use DbkIrrf\Dominio\DTO\RegistroRendimentoIsento84DTO;
use DbkIrrf\Dominio\DTO\RegistroRraDTO;
use DbkIrrf\Dominio\DTO\RegistroTribExclusivaDTO;
use DbkIrrf\Dominio\Enum\SubTipoInvestimento;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Gerador\GeradorBemDireito;
use DbkIrrf\Infraestrutura\Gerador\GeradorDependente;
use DbkIrrf\Infraestrutura\Gerador\GeradorDivida;
use DbkIrrf\Infraestrutura\Gerador\GeradorInvestExterior;
use DbkIrrf\Infraestrutura\Gerador\GeradorPagamento;
use DbkIrrf\Infraestrutura\Gerador\GeradorRra;
use DbkIrrf\Infraestrutura\Gerador\GeradorRendimentoIsento84;
use DbkIrrf\Infraestrutura\Gerador\GeradorTribExclusiva;
use DbkIrrf\Infraestrutura\Leitor\LeitorBemDireito;
use DbkIrrf\Infraestrutura\Leitor\LeitorDependente;
use DbkIrrf\Infraestrutura\Leitor\LeitorDivida;
use DbkIrrf\Infraestrutura\Leitor\LeitorInvestExterior;
use DbkIrrf\Infraestrutura\Leitor\LeitorPagamento;
use DbkIrrf\Infraestrutura\Leitor\LeitorRra;
use DbkIrrf\Infraestrutura\Leitor\LeitorRendimentoIsento84;
use DbkIrrf\Infraestrutura\Leitor\LeitorTribExclusiva;
use PHPUnit\Framework\TestCase;

final class GeradorComplementaresTest extends TestCase
{
    // ========== Registro 25 - Dependentes ==========

    public function testReg25TamanhoEroundTrip(): void
    {
        $dto = new RegistroDependenteDTO(
            cpf: new Cpf('41653508000'),
            sequencial: 1,
            tipoDependente: new CodigoDependente(CodigoDependente::FILHO_ATE_21),
            nomeDependente: 'RYAN SILVA MONTANO',
            dataNascimento: Data::deDateTime(new \DateTime('2021-03-15')),
            cpfDependente: new Cpf('13480293077'),
        );

        $linha = (new GeradorDependente())->gerar($dto);
        $this->assertSame(224, strlen($linha));

        /** @var RegistroDependenteDTO $lido */
        $lido = (new LeitorDependente())->ler($linha);

        $this->assertSame('41653508000', $lido->cpf->valor);
        $this->assertSame(1, $lido->sequencial);
        $this->assertSame(CodigoDependente::FILHO_ATE_21, $lido->tipoDependente->valor);
        $this->assertSame('RYAN SILVA MONTANO', $lido->nomeDependente);
        $this->assertSame('15032021', $lido->dataNascimento->valor);
        $this->assertSame('13480293077', $lido->cpfDependente->valor);
    }

    // ========== Registro 26 - Pagamentos ==========

    public function testReg26TamanhoEroundTrip(): void
    {
        $dto = new RegistroPagamentoDTO(
            cpf: new Cpf('41653508000'),
            cpfCnpjBeneficiario: '66313835018',
            nomeBeneficiario: 'EZEQUIEL LUIS MENDONCA',
            valorPago: ValorMonetario::deCentavos(500000),
            parcelaNaoDedutivel: ValorMonetario::deCentavos(111100),
            descricao: 'PAGAMENTO DE MEDICO',
        );

        $linha = (new GeradorPagamento())->gerar($dto);
        $this->assertSame(671, strlen($linha));

        /** @var RegistroPagamentoDTO $lido */
        $lido = (new LeitorPagamento())->ler($linha);

        $this->assertSame('66313835018', $lido->cpfCnpjBeneficiario);
        $this->assertSame('EZEQUIEL LUIS MENDONCA', $lido->nomeBeneficiario);
        $this->assertSame(5000.00, $lido->valorPago->emReais());
        $this->assertSame(1111.00, $lido->parcelaNaoDedutivel->emReais());
        $this->assertSame('PAGAMENTO DE MEDICO', $lido->descricao);
    }

    // ========== Registro 27 - Bens e Direitos ==========

    public function testReg27ImovelTamanhoEcamposChave(): void
    {
        $dto = new RegistroBemDireitoDTO(
            cpf: new Cpf('41653508000'),
            codigoItem: '11',
            flagExterior: '0',
            pais: '105',
            descricao: 'APARTAMENTO PETROPOLIS',
            valorAnterior: ValorMonetario::deCentavos(10000000),
            valorAtual: ValorMonetario::deCentavos(20000000),
            logradouro: 'AV KOELER',
            numero: '260',
            complemento: 'PREDIO',
            bairro: 'CENTRO',
            cep: '25840600',
            uf: 'RJ',
            codigoMunicipioIbge: '5877',
            municipio: 'PETROPOLIS',
        );

        $linha = (new GeradorBemDireito())->gerar($dto);
        $this->assertSame(1251, strlen($linha));

        /** @var RegistroBemDireitoDTO $lido */
        $lido = (new LeitorBemDireito())->ler($linha);

        $this->assertSame('11', $lido->codigoItem);
        $this->assertSame('0', $lido->flagExterior);
        $this->assertSame('APARTAMENTO PETROPOLIS', $lido->descricao);
        $this->assertSame(100000.00, $lido->valorAnterior->emReais());
        $this->assertSame(200000.00, $lido->valorAtual->emReais());
        $this->assertSame('AV KOELER', $lido->logradouro);
    }

    public function testReg27DepositoBancario(): void
    {
        $dto = new RegistroBemDireitoDTO(
            cpf: new Cpf('41653508000'),
            codigoItem: '01',
            pais: '105',
            descricao: 'TESTE DEPOSITO EM CONTA CORRENTE',
            valorAnterior: ValorMonetario::deCentavos(5000000),
            valorAtual: ValorMonetario::deCentavos(6000000),
            agencia: '2487',
            dvConta: '6',
            numeroConta: '0000000025878',
        );

        $linha = (new GeradorBemDireito())->gerar($dto);
        $this->assertSame(1251, strlen($linha));

        /** @var RegistroBemDireitoDTO $lido */
        $lido = (new LeitorBemDireito())->ler($linha);

        $this->assertSame('2487', $lido->agencia);
        $this->assertSame('6', $lido->dvConta);
        $this->assertSame('0000000025878', $lido->numeroConta);
    }

    // ========== Registro 28 - Dividas ==========

    public function testReg28TamanhoEcamposQuebrados(): void
    {
        $dto = new RegistroDividaDTO(
            cpf: new Cpf('41653508000'),
            codigoDivida: '11',
            descricao: 'FINANCIAMENTO IMOBILIARIO CAIXA',
            saldoAnterior: ValorMonetario::deCentavos(17555577),
            saldoAtual: ValorMonetario::deCentavos(18833344),
            valorPagoAno: ValorMonetario::deCentavos(3322211),
        );

        $linha = (new GeradorDivida())->gerar($dto);
        $this->assertSame(576, strlen($linha));

        /** @var RegistroDividaDTO $lido */
        $lido = (new LeitorDivida())->ler($linha);

        $this->assertSame('FINANCIAMENTO IMOBILIARIO CAIXA', $lido->descricao);
        $this->assertSame(175555.77, $lido->saldoAnterior->emReais());
        $this->assertSame(188333.44, $lido->saldoAtual->emReais());
        $this->assertSame(33222.11, $lido->valorPagoAno->emReais());
    }

    // ========== Registro 45 - Rendimentos Recebidos Acumuladamente (RRA) ==========

    public function testReg45TamanhoEcamposRRA(): void
    {
        $rendimentos = ValorMonetario::deCentavos(85465); // R$ 854,65

        $dto = new RegistroRraDTO(
            cpf: new Cpf('71926456130'),
            cnpjFontePagadora: new Cnpj('27865757000102'),
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A 123',
            contribPrevidenciaria: ValorMonetario::deCentavos(33365),
            impostoRetidoFonte: ValorMonetario::deCentavos(2164),
            mesRecebimentoRRA: '03',
            numMesesRRA: '3',
            impostoBrutoRRA: ValorMonetario::deCentavos(0),
            rendimentosRRA: $rendimentos,
            rendimentosRRACopia: $rendimentos,
        );

        $linha = (new GeradorRra())->gerar($dto);
        $this->assertSame(216, strlen($linha));

        // Pos 90-102 deve ser zeros
        $this->assertSame('0000000000000', substr($linha, 89, 13));

        /** @var RegistroRraDTO $lido */
        $lido = (new LeitorRra())->ler($linha);

        $this->assertSame(333.65, $lido->contribPrevidenciaria->emReais());
        $this->assertSame(21.64, $lido->impostoRetidoFonte->emReais());
        $this->assertSame('03', $lido->mesRecebimentoRRA);
        $this->assertSame('3', $lido->numMesesRRA);
        $this->assertSame(0.00, $lido->impostoBrutoRRA->emReais());
        $this->assertSame(854.65, $lido->rendimentosRRA->emReais());      // pos 168-180
        $this->assertSame(854.65, $lido->rendimentosRRACopia->emReais()); // pos 181-193
    }

    // ========== Registro 84 - Rendimentos Isentos ==========

    public function testReg84TamanhoEround(): void
    {
        $dto = new RegistroRendimentoIsento84DTO(
            cpf: new Cpf('41653508000'),
            tipoBeneficiario: TipoBeneficiario::TITULAR,
            cpfBeneficiario: new Cpf('41653508000'),
            codigoTipoRendimento: '0001',
            cnpjFontePagadora: '27865757000102',
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            valorRendimentoIsento: ValorMonetario::deCentavos(500000),
        );

        $linha = (new GeradorRendimentoIsento84())->gerar($dto);
        $this->assertSame(144, strlen($linha));

        /** @var RegistroRendimentoIsento84DTO $lido */
        $lido = (new LeitorRendimentoIsento84())->ler($linha);

        $this->assertSame(TipoBeneficiario::TITULAR, $lido->tipoBeneficiario);
        $this->assertSame('0001', $lido->codigoTipoRendimento);
        $this->assertSame(5000.00, $lido->valorRendimentoIsento->emReais());
    }

    // ========== Registro 88 - Trib Exclusiva ==========

    public function testReg88TamanhoEround(): void
    {
        $dto = new RegistroTribExclusivaDTO(
            cpf: new Cpf('41653508000'),
            tipoBeneficiario: TipoBeneficiario::TITULAR,
            cpfBeneficiario: new Cpf('41653508000'),
            codigoTipoRendimento: '0006',
            cnpjFontePagadora: new Cnpj('27865757000102'),
            nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
            valorRendimento: ValorMonetario::deCentavos(1000000),
        );

        $linha = (new GeradorTribExclusiva())->gerar($dto);
        $this->assertSame(131, strlen($linha));

        /** @var RegistroTribExclusivaDTO $lido */
        $lido = (new LeitorTribExclusiva())->ler($linha);

        $this->assertSame('0006', $lido->codigoTipoRendimento);
        $this->assertSame(10000.00, $lido->valorRendimento->emReais());
    }

    // ========== Registro 37 - Invest Exterior ==========

    public function testReg37TamanhoEsubTipos(): void
    {
        $gerador = new GeradorInvestExterior();
        $leitor = new LeitorInvestExterior();

        // Sub-tipo 1 - Aplicacoes Financeiras
        $dto1 = new RegistroInvestExteriorDTO(
            cpf: new Cpf('41653508000'),
            idBem: '00002',
            sequencialDetalhe: '00001',
            subTipo: SubTipoInvestimento::APLICACOES_FINANCEIRAS,
            rendimentoValor: ValorMonetario::deCentavos(636545),
            impostoDevido15: ValorMonetario::deCentavos(95481),
            impostoPagoExterior: ValorMonetario::deCentavos(765236),
        );

        $linha1 = $gerador->gerar($dto1);
        $this->assertSame(103, strlen($linha1));

        /** @var RegistroInvestExteriorDTO $lido1 */
        $lido1 = $leitor->ler($linha1);
        $this->assertSame('00002', $lido1->idBem);
        $this->assertSame(SubTipoInvestimento::APLICACOES_FINANCEIRAS, $lido1->subTipo);
        $this->assertSame(6365.45, $lido1->rendimentoValor->emReais());
        $this->assertSame(954.81, $lido1->impostoDevido15->emReais());
        $this->assertSame(7652.36, $lido1->impostoPagoExterior->emReais());

        // Sub-tipo 2 - Lucros e Dividendos
        $dto2 = new RegistroInvestExteriorDTO(
            cpf: new Cpf('41653508000'),
            idBem: '00002',
            sequencialDetalhe: '00002',
            subTipo: SubTipoInvestimento::LUCROS_DIVIDENDOS,
            rendimentoValor: ValorMonetario::deCentavos(236528),
            impostoDevido15: ValorMonetario::deCentavos(35479),
            impostoPagoExterior: ValorMonetario::deCentavos(354299),
        );

        $linha2 = $gerador->gerar($dto2);
        /** @var RegistroInvestExteriorDTO $lido2 */
        $lido2 = $leitor->ler($linha2);
        $this->assertSame(SubTipoInvestimento::LUCROS_DIVIDENDOS, $lido2->subTipo);
        $this->assertSame(2365.28, $lido2->rendimentoValor->emReais());
    }
}
