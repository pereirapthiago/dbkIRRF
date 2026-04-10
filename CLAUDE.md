# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Rodar todos os testes
composer test

# Rodar apenas testes unitários
vendor/bin/phpunit --testsuite Unit

# Rodar apenas testes de integração
vendor/bin/phpunit --testsuite Integration

# Rodar um único arquivo de teste
vendor/bin/phpunit tests/Unit/Infraestrutura/Leitor/LeitorHeaderTest.php

# Rodar um único método de teste
vendor/bin/phpunit --filter nomeDoMetodo tests/Unit/...
```

## Arquitetura

Biblioteca PHP (namespace `DbkIrrf\`) para gerar e ler arquivos `.DBK` do IRPF 2026 da Receita Federal. Cada arquivo `.DBK` é composto por linhas de tamanho fixo, onde os primeiros 2-4 caracteres identificam o tipo de registro.

### Camadas

**Domínio** (`src/Dominio/`)
- `DTO/` — DTOs imutáveis (readonly quando possível) para cada tipo de registro. `DeclaracaoDTO` agrega todos os registros de uma declaração.
- `Enum/TipoRegistro` — enum backed `string` com o código de cada registro (`IRPF`, `16`, `21`, ..., `T9`). Centraliza tamanho de linha e identificação por prefixo (`identificarPorLinha()`).
- `ValorObjeto/` — `Cpf`, `Cnpj`, `ValorMonetario`, `Data`, `Checksum` — imutáveis, validam no construtor.
- `Contrato/` — interfaces `GeradorRegistroInterface`, `LeitorRegistroInterface`, `RegistroInterface`.

**Aplicação** (`src/Aplicacao/`)
- `GeradorDbk` — percorre `DeclaracaoDTO` em ordem canônica e delega cada registro ao gerador correto.
- `LeitorDbk` — lê linha a linha, identifica o tipo pelo prefixo e delega ao leitor correto.
- `FabricaRegistro` — mapeia `TipoRegistro` → instância de gerador/leitor (Strategy + Factory).
- `MapeadorDeclaracao` — converte estruturas externas (ex: JSON) para `DeclaracaoDTO`.
- `NomeadorArquivo` — gera o nome canônico do arquivo (`CPF-IRPF-A-ANO-ANO-TIPO.DBK`).

**Infraestrutura** (`src/Infraestrutura/`)
- `Gerador/GeradorRegistroBase` — base abstrata: chama `gerarCampos()` (template method), aplica checksum placeholder e valida tamanho da linha.
- `Leitor/LeitorRegistroBase` — base abstrata: valida tamanho da linha, extrai campos com posições 1-based conforme documentação DBK.
- `Formatador/` — `FormatadorTexto` (pad/truncate à direita), `FormatadorNumerico` (pad à esquerda com zeros), `FormatadorMonetario` (centavos sem separador), `FormatadorData`.
- `Validador/ValidadorRegistro` — valida tamanhos de linha de um arquivo inteiro.

### Fluxo para adicionar um novo tipo de registro

1. Criar DTO em `src/Dominio/DTO/RegistroXxxDTO.php` implementando `RegistroInterface`.
2. Adicionar `case` em `TipoRegistro` com código, descrição e tamanho da linha.
3. Criar `src/Infraestrutura/Gerador/GeradorXxx.php` estendendo `GeradorRegistroBase`, implementar `gerarCampos()` e `suportaTipo()`.
4. Criar `src/Infraestrutura/Leitor/LeitorXxx.php` estendendo `LeitorRegistroBase`, implementar `lerCampos()` e `suportaTipo()`.
5. Registrar gerador e leitor em `FabricaRegistro`.
6. Adicionar propriedade/métodos `adicionar`/`obter` em `DeclaracaoDTO`.
7. Adicionar ao fluxo de `GeradorDbk::gerar()` e ao `match` de `LeitorDbk::ler()`.

### Checksum

O algoritmo real dos últimos 10 dígitos de cada linha não foi descoberto. `Checksum::placeholder()` retorna `0000000000`. Todos os geradores usam o placeholder — não alterar sem o algoritmo real.

### Convenções obrigatórias

- Todo o código em **português** (nomes de variáveis, métodos, classes, comentários).
- Sem arrays genéricos: usar DTOs tipados e `list<TipoDTO>` com PHPDoc.
- ENUMs para todos os valores categóricos.
- Princípios SOLID: cada gerador/leitor cuida de um único tipo de registro.
- Posições de campos nos leitores são **1-based** (conforme documentação oficial do DBK).
- Linhas terminam com `\r\n`; o arquivo inteiro também termina com `\r\n`.
