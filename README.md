# DBK IRRF - Pacote PHP para IRPF 2026

Pacote PHP para geracao e leitura de arquivos `.DBK` compativeis com o programa IRPF 2026 da Receita Federal do Brasil.

## Requisitos

- PHP 8.2+
- Composer

## Instalacao

```bash
composer require atlantic/dbk-irrf
```

## Uso Rapido

### Gerar um arquivo DBK

```php
use DbkIrrf\Aplicacao\Fabrica\FabricaDeclaracao;
use DbkIrrf\Aplicacao\Servico\GeradorDbk;
use DbkIrrf\Aplicacao\Servico\NomeadorArquivo;
use DbkIrrf\Dominio\DTO\RegistroRendimentosPJDTO;
use DbkIrrf\Dominio\Enum\EstadoCivil;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\Enum\UnidadeFederativa;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

// 1. Criar declaracao base
$fabrica = new FabricaDeclaracao();
$declaracao = $fabrica->criar(
    cpf: new Cpf('41653508000'),
    anoExercicio: 2026,
    anoCalendario: 2025,
    tipoDeclaracao: TipoDeclaracao::ORIGINAL,
);

// 2. Preencher dados do header
$declaracao->header = new \DbkIrrf\Dominio\DTO\RegistroHeaderDTO(
    cpf: new Cpf('41653508000'),
    anoExercicio: 2026,
    anoCalendario: 2025,
    tipoDeclaracao: TipoDeclaracao::ORIGINAL,
    nome: 'JORGE LUCAS DA SILVA MONTANO',
    uf: UnidadeFederativa::RJ,
    dataNascimento: Data::deDateTime(new \DateTime('2000-10-10')),
    estadoCivil: EstadoCivil::SOLTEIRO,
    codigoMunicipioIbge: '5877',
    cep: '25845060',
    cidade: 'PETROPOLIS',
    cnpjFontePrincipal: new Cnpj('27865757000102'),
    impostoAPagar: ValorMonetario::deCentavos(1480109),
);

// 3. Adicionar rendimentos PJ
$declaracao->adicionarRendimentoPJ(new RegistroRendimentosPJDTO(
    cpf: new Cpf('41653508000'),
    cnpjFontePagadora: new Cnpj('27865757000102'),
    nomeFontePagadora: 'GLOBO COMUNICACAO E PARTICIPACOES S/A',
    rendimentosRecebidos: ValorMonetario::deReais(180000.00),
    contribPrevidenciaria: ValorMonetario::deReais(12000.00),
    impostoRetidoFonte: ValorMonetario::deReais(50000.00),
));

// 4. Gerar arquivo
$gerador = new GeradorDbk();
$nomeador = new NomeadorArquivo();

$nomeArquivo = $nomeador->gerarDeDeclaracao($declaracao);
// Resultado: "41653508000-IRPF-A-2026-2025-ORIGI.DBK"

$gerador->gerarParaArquivo($declaracao, "/caminho/{$nomeArquivo}");
```

### Ler um arquivo DBK existente

```php
use DbkIrrf\Aplicacao\Servico\LeitorDbk;

$leitor = new LeitorDbk();
$declaracao = $leitor->lerDeArquivo('/caminho/41653508000-IRPF-A-2026-2025-ORIGI.DBK');

echo $declaracao->header->nome; // "JORGE LUCAS DA SILVA MONTANO"
echo $declaracao->header->impostoAPagar->emReais(); // 14801.09

foreach ($declaracao->obterRendimentosPJ() as $rendimento) {
    echo $rendimento->nomeFontePagadora;
    echo $rendimento->rendimentosRecebidos->emReais();
}
```

### Validar um arquivo DBK

```php
use DbkIrrf\Infraestrutura\Validador\ValidadorRegistro;

$validador = new ValidadorRegistro();
$resultado = $validador->validarArquivo(file_get_contents('arquivo.DBK'));

if ($resultado->valido) {
    echo "Arquivo valido!";
} else {
    foreach ($resultado->erros as $erro) {
        echo $erro . "\n";
    }
}
```

## Registros Suportados

| Tipo | Descricao | Tamanho |
|------|-----------|---------|
| IRPF | Header/Cabecalho | 1244 |
| 16 | Dados Pessoais | 930 |
| 21 | Rendimentos Tributaveis PJ | 170 |
| 22 | Rendimentos Mensais PF/Exterior | 167 |
| 23 | Imposto Pago/Retido | 40 |
| 24 | Deducoes Legais | 40 |
| 25 | Dependentes | 224 |
| 26 | Pagamentos Efetuados | 671 |
| 27 | Bens e Direitos | 1251 |
| 28 | Dividas e Onus Reais | 576 |
| 37 | Invest. Exterior (Lei 14.754) | 103 |
| 45 | Rendimentos Isentos PJ | 216 |
| 84 | Rendimentos Isentos | 144 |
| 88 | Trib. Exclusiva/Definitiva | 131 |
| T9 | Trailer/Totalizador | 449 |

## Arquitetura

```
src/
├── Dominio/          # ENUMs, DTOs, Value Objects, Contratos
├── Aplicacao/        # Servicos (GeradorDbk, LeitorDbk), Fabricas
└── Infraestrutura/   # Geradores, Leitores, Formatadores, Validadores
```

- **DDD + Clean Architecture**
- **DTOs fortemente tipados** (zero arrays genericos)
- **ENUMs** para valores categoricos
- **Value Objects** imutaveis (Cpf, Cnpj, ValorMonetario, Data, Checksum)
- **Strategy Pattern**: cada registro tem seu gerador/leitor
- **Factory Pattern**: FabricaRegistro mapeia tipo -> gerador/leitor

## Testes

```bash
composer test
```

## Limitacoes

- **Checksum**: O algoritmo de checksum dos ultimos 10 digitos de cada linha nao foi descoberto. Linhas geradas usam placeholder `0000000000`.
- **Registros 19 e 20** (Resumo Rendimentos e Resumo Calculos): fora do escopo. Ao ler um arquivo que os contenha, serao ignorados.

## Licenca

MIT
