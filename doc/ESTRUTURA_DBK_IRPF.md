# Engenharia Reversa - Arquivo .DBK IRPF 2026/2025

## Visao Geral

- **Arquivo analisado**: `41653508000-IRPF-A-2026-2025-ORIGI.DBK`
- **Formato**: Texto posicional (largura fixa), 1 registro por linha
- **Encoding**: ASCII/Latin-1
- **Total de linhas**: 29 registros + 1 linha vazia final
- **Convencao monetaria**: **CONFIRMADO** - Valores em centavos (sem ponto/virgula), preenchidos com zeros a esquerda. Ex: `0000018000000` = 18.000.000 centavos = R$ 180.000,00
- **Checksum**: Ultimos 10 digitos de cada linha sao um hash/checksum de validacao

---

## Tipos de Registro Identificados

| Tipo | Descricao                          | Qtd | Tamanho (chars) |
|------|------------------------------------|-----|-----------------|
| IRPF | Header / Cabecalho da declaracao   | 1   | 1244            |
| 16   | Dados pessoais do contribuinte     | 1   | 930             |
| 19   | Resumo rendimentos por fonte       | 1   | 346             |
| 20   | Resumo calculos / totais           | 1   | 926             |
| 21   | Rendimentos tributaveis (PJ)       | 1   | 170             |
| 22   | Rendimentos mensais (carne-leao)   | 12  | 167             |
| 23   | Imposto pago / retido (cod)        | 1   | 40              |
| 24   | Deducoes legais (cod)              | 3   | 40              |
| 25   | Dependentes                        | 1   | 224             |
| 26   | Pagamentos efetuados               | 1   | 671             |
| 27   | Bens e direitos                    | 1   | 1251            |
| 28   | Dividas e onus reais               | 1   | 576             |
| 45   | Rendimentos isentos (PJ)           | 1   | 216             |
| 84   | Carnê-leão / imposto complementar  | 1   | 144             |
| 39   | Declaracao de saida definitiva     | 0-1 | 193             |
| 88   | Imposto pago código DARF           | 1   | 131             |
| T9   | Trailer / Totalizador              | 1   | 449             |

---

## REGISTRO: IRPF (Header) - 1244 caracteres

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                        | Valor Encontrado                   | Confianca |
|---------|---------|-----|--------|------------------------------|------------------------------------|-----------|
| 1       | 4       | 4   | Alfa   | Identificador sistema        | "IRPF"                             | ALTA      |
| 5       | 8       | 4   | Alfa   | Reservado/espacos            | "    "                             | MEDIA     |
| 9       | 12      | 4   | Num    | Ano exercicio                | "2026"                             | ALTA      |
| 13      | 16      | 4   | Num    | Ano calendario (base)        | "2025"                             | ALTA      |
| 17      | 18      | 2   | Num    | Codigo/versao                | "36"                               | MEDIA     |
| 19      | 20      | 2   | Num    | **Tipo declaracao**          | "00"=Ajuste Anual, "20"=Saida Definitiva | ALTA |
| 21      | 21      | 1   | Alfa   | **Separador tipo declaracao**| "0" (Ajuste) / " " (Saida) - OBRIGATORIO mudar junto com pos 19-20 | ALTA      |
| 22      | 32      | 11  | Num    | CPF do contribuinte          | "41653508000"                      | ALTA      |
| 33      | 35      | 3   | Alfa   | Reservado/espacos            | "   "                              | BAIXA     |
| 36      | 39      | 4   | Num    | **Codigo ocupacao (hipotese)**| "1100" (JORGE/THIAGO) / "0001" (ADEM) - varia por arquivo; hipotese revisada: e ocupacao | MEDIA     |
| 40      | 99      | 60  | Alfa   | Nome completo                | "JORGE LUCAS DA SILVA MONTANO"     | ALTA      |
| 100     | 101     | 2   | Alfa   | UF                           | "RJ"                               | ALTA      |
| 102     | 112     | 11  | Num    | Hash/valor calculado         | "35992361700" (NAO e titulo eleitor - muda com QUALQUER dado alterado, inclusive flags pessoais) | BAIXA     |
| 113     | 120     | 8   | Data   | Data nascimento (ddmmaaaa)   | "10102000" (10/10/2000)            | ALTA      |
| 121     | 121     | 1   | Alfa   | Estado civil (S/C/D/V)       | "S" (Solteiro)                     | ALTA      |
| 122     | 122     | 1   | Num    | Tipo declaracao              | "1"                                | BAIXA     |
| 123     | 123     | 1   | Alfa   | Flag (S/N)                   | "S"                                | BAIXA     |
| 124     | 134     | 11  | Alfa   | Reservado                    | (espacos)                          | BAIXA     |
| 135     | 148     | 14  | Alfa   | Sistema operacional          | "WINDOWS 11   "                    | ALTA      |
| 149     | 154     | 6   | Alfa   | Versao SO                    | "10.0  "                           | ALTA      |
| 155     | 166     | 12  | Alfa   | Versao programa IRPF         | " 17.0.16   "                      | ALTA      |
| 167     | 174     | 8   | Alfa   | Reservado                    | (espacos)                          | BAIXA     |
| 175     | 178     | 4   | Num    | Cod municipio (IBGE)         | "5877"                             | ALTA      |
| 179     | 189     | 11  | Num    | **CPF do conjuge/companheiro** | (vazio se solteiro; "13103517760" se casado) | ALTA |
| 190     | 190     | 1   | Alfa   | Reservado/separador          | (espaco)                           | BAIXA     |
| 191     | 203     | 13  | Num    | Recibo / numero controle     | "10000008315109"?                  | BAIXA     |
| 204     | 213     | 10  | Alfa   | Reservado                    | (espacos)                          | BAIXA     |
| 214     | 217     | 4   | Num    | Codigo endereco/municipio    | "1351" (muda com endereco)         | MEDIA     |
| 218     | 218     | 1   | Num    | **Flag identificacao contrib** | "0"→"1" (mudou ao marcar flags ID) | MEDIA    |
| 219     | 226     | 8   | Num    | CEP                          | "25845060"                         | ALTA      |
| 227     | 246     | 20  | Num    | Zeros / reservado            | (zeros)                            | BAIXA     |
| 247     | 253     | 7   | Num    | Imposto a pagar (centavos)   | "1480109" (R$ 14.801,09)           | ALTA      |
| 254     | 254     | 1   | Num    | Flag                         | "1"                                | BAIXA     |
| 255     | 265     | 11  | Num    | CPF (repetido)               | "41653508000"                      | ALTA      |
| 266     | 305     | 40  | Alfa   | Reservado                    | (espacos/zeros)                    | BAIXA     |
| 306     | 330     | 25  | Num    | Zeros                        | (zeros)                            | BAIXA     |
| 331     | 344     | 14  | Num    | CNPJ fonte principal         | "27865757000102"                   | ALTA      |
| 345     | 384     | 40  | Alfa   | Reservado                    | (espacos)                          | BAIXA     |
| 385     | 395     | 11  | Num    | CPF dependente/conjuge       | "13480293077"                      | MEDIA     |
| 396     | 403     | 8   | Data   | Data nasc dependente         | "15032021" (15/03/2021)            | MEDIA     |
| 404     | 500     | 97  | Alfa   | Reservado                    | (espacos)                          | BAIXA     |
| 501     | 511     | 11  | Num    | CPF medico/terceiro          | "66313835018"                      | MEDIA     |
| 512     | 550     | 39  | Alfa   | Reservado                    | (espacos)                          | BAIXA     |
| 551     | 590     | 40  | Alfa   | Cidade                       | "PETROPOLIS"                       | ALTA      |
| 591     | 650     | 60  | Alfa   | Nome (repetido)              | "JORGE LUCAS DA SILVA MONTANO"     | ALTA      |
| 651     | 660     | 10  | Num    | Hash / numero recibo         | "1618290403"                       | BAIXA     |
| 661     | 672     | 12  | Alfnum | Codigo controle/hash         | " 107C61A51159" (Ajuste) / " A83B76EC0D69" (Saida) | BAIXA |
| 673     | 680     | 8   | Data   | **Data saida (nao residente)** | "00000000" (Ajuste) / "20251010" (AAAAMMDD) - Data nao residente | ALTA |
| 681     | 681     | 1   | Num    | **Flag procurador**          | "0" (sem) / "1" (com procurador)   | ALTA      |
| 682     | 692     | 11  | Num    | **CPF procurador (saida)**   | (vazio se Ajuste; "98736521263" se Saida) | ALTA |
| 693     | 700     | 8   | Num    | Reservado/zeros              | (zeros)                            | BAIXA     |
| 701     | 713     | 13  | Num    | Valor (centavos)             | "36000000     "                    | MEDIA     |
| 714     | 725     | 12  | Alfa   | Reservado                    | (espacos/zeros)                    | BAIXA     |
| 726     | 778     | 53  | Num    | Valores financeiros          | Multiplos valores                  | MEDIA     |
| 779     | 792     | 14  | Alfa   | Reservado                    | (espacos/zeros)                    | BAIXA     |
| 793     | 793     | 1   | Num    | **Cor/Raca (codigo)**        | "2" BASE / "1" = Indigena (var01_v2) | ALTA    |
| 794     | 860     | 67  | Alfa   | Reservado                    | (espacos/zeros)                    | BAIXA     |
| 861     | 880     | 20  | Alfnum | Flag + CPF medico repetido   | "10000000 66313835018"             | MEDIA     |
| 881     | 1190    | 310 | Alfa   | Reservado / padding          | (espacos + zeros esparsos)         | BAIXA     |
| 1191    | 1198    | 8   | Data   | **Data residencia no pais**  | (vazio se nao informado; "10112024" = 10/11/2024) | ALTA |
| 1199    | 1234    | 36  | Alfa   | Reservado / padding          | (espacos/zeros)                    | BAIXA     |
| 1235    | 1244    | 10  | Num    | **Checksum**                 | "3267140398"                       | ALTA      |

**Evidencia - Variacao 12_saida** (declaracao de saida definitiva):
- **Tipo declaracao (pos 19-20)**: "00" → "20" (Saida Definitiva) ✓
- **Separador tipo (pos 21)**: "0" → " " (espaco) — OBRIGATORIO alterar junto com pos 19-20; sistema da Receita nao reconhece como Saida se pos 21 continuar "0" ✓
- **Data saida nao residente (pos 673-680)**: "00000000" → "20251010" (10/10/2025 em AAAAMMDD) ✓
- **Flag procurador (pos 681)**: "0" → "1" ✓
- **CPF procurador (pos 682-692)**: espacos → "98736521263" ✓
- **NOTA**: Arquivo .DBK muda de `IRPF-A-` para `IRPF-S-` no nome
- **BUG IDENTIFICADO**: Comparacao com arquivo gerado pelo nosso sistema (71930207140) revelou que pos 21 continuava "0" mesmo com pos 19-20="20", causando falha no reconhecimento do tipo pelo sistema da Receita

**Evidencia - Variacao 01_v2** (flags ID, CPF conjuge, data residencia, cor/raca):
- **CPF conjuge (pos 179-189)**: espacos → "13103517760" ✓ (confirmado; campo era "Reservado")
- **Cor/Raca (pos 793)**: "2" → "1" (usuario mudou para Indigena; logo 1=Indigena neste encoding) ✓
- **Data residencia no pais (pos 1191-1198)**: espacos → "10112024" (10/11/2024) ✓
- **Flag ID (pos 218)**: "0" → "1" (um dos flags marcados na identificacao do contribuinte)
- **EFEITO COLATERAL**: pos 102-112 (hash) recalculado com qualquer mudanca de dados
- **NOTA COR/RACA**: encoding provavel: 1=Indigena, 2=Branca(?)... verificar com variacao futura

---

## REGISTRO 16 - Dados Pessoais - 930 caracteres

**Atualizado via variacoes 01 e 01_v2** (nome/endereco, flags ID, CPF conjuge, data residencia).

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                         | Valor Encontrado                   | Confianca |
|---------|---------|-----|--------|-------------------------------|------------------------------------|-----------|
| 1       | 2       | 2   | Num    | Tipo registro                 | "16"                               | ALTA      |
| 3       | 13      | 11  | Num    | CPF contribuinte              | "41653508000"                      | ALTA      |
| 14      | 73      | 60  | Alfa   | Nome completo                 | "JORGE LUCAS DA SILVA MONTANO"     | ALTA      |
| 74      | 88      | 15  | Alfa   | Tipo logradouro               | "RUA            "                  | ALTA      |
| 89      | 128     | 40  | Alfa   | Logradouro                    | "AV KOELER"                        | ALTA      |
| 129     | 134     | 6   | Alfnum | Numero                        | "260   "                           | ALTA      |
| 135     | 154     | 20  | Alfa   | Complemento                   | "CASA"                             | ALTA      |
| 155     | 155     | 1   | Alfa   | **Separador obrigatorio**     | " " (SEMPRE espaco)                | ALTA      |
| 156     | 174     | 19  | Alfa   | Bairro                        | "CENTRO"                           | ALTA      |
| 175     | 182     | 8   | Num    | CEP                           | "25845060"                         | ALTA      |
| 183     | 183     | 1   | Alfa   | Separador                     | " "                                | MEDIA     |
| 184     | 187     | 4   | Num    | Cod municipio (IBGE)          | "5877"                             | ALTA      |
| 188     | 227     | 40  | Alfa   | Municipio                     | "PETROPOLIS"                       | ALTA      |
| 228     | 229     | 2   | Alfa   | UF                            | "RJ"                               | ALTA      |
| 230     | 235     | 6   | Alfnum | Reservado/codigo              | "   105"                           | BAIXA     |
| 236     | 295     | 60  | Alfa   | Email                         | "JORGEMONTANO@GMAIL.COM"           | ALTA      |
| 296     | 336     | 41  | Alfa   | Reservado                     | (espacos)                          | BAIXA     |
| 337     | 347     | 11  | Num    | **CPF do conjuge/companheiro**| (vazio se solteiro; "13103517760" se casado) | ALTA |
| 348     | 349     | 2   | Num    | **DDD telefone fixo**         | "24" / "  " (espacos) se vazio     | ALTA      |
| 350     | 360     | 11  | Alfa   | Reservado                     | (espacos)                          | BAIXA     |
| 361     | 368     | 8   | Data   | Data nascimento (ddmmaaaa)    | "10102000" (10/10/2000)            | ALTA      |
| 369     | 381     | 13  | Alfa   | Reservado                     | (espacos)                          | BAIXA     |
| 382     | 384     | 3   | Num    | Ocupacao/campo desconhecido   | "000" (JORGE) / "120" (THIAGO old) / "   " (THIAGO new) — REVISAR | BAIXA |
| 385     | 386     | 2   | Num    | Codigo desconhecido           | "01" (JORGE/THIAGO new) / "02" (THIAGO old) — mudou ao adicionar conjuge | BAIXA |
| 387     | 387     | 1   | Alfa   | Desconhecido                  | "1" — igual em todos arquivos, nunca mudou | BAIXA |
| 388     | 388     | 1   | Alfa   | Hipotese: Endereco Brasil/Exterior | "S" — sempre S; candidato: radio "Brasil" (todos arquivos = Brasil) | BAIXA |
| 389     | 389     | 1   | Alfa   | Hipotese: Ha declarante com doenca grave ou deficiencia? | "N" — sempre N; candidato: checkbox desmarcado em todos os testes | BAIXA |
| 390     | 390     | 1   | Alfa   | Desconhecido                  | "S" — sempre S; nenhum campo visivel explica valor constante S | BAIXA |
| 391     | 391     | 1   | Alfa   | **Houve alteracao cadastral?**| "N"=Nao / "S"=Sim — confirmado por var01 (mudanca endereco) e var01_v2 (adicao conjuge) | ALTA |
| 392     | 443     | 52  | Alfa   | Reservado (contem CNPJ fonte) | (espacos + CNPJ no final)          | BAIXA     |
| 444     | 453     | 10  | Num    | **Numero recibo (base 10d)**  | "4082548219" (BASE transmitida); espacos se nao transmitida | ALTA |
| 454     | 454     | 1   | Alfa   | **Situacao declaracao**       | "A"=Aberta/Aguardando, "S"=Saida/Entregue | ALTA |
| 455     | 485     | 31  | Alfa   | Reservado                     | (espacos)                          | BAIXA     |
| 486     | 487     | 2   | Num    | DDD celular                   | "24"                               | ALTA      |
| 488     | 496     | 9   | Num    | Numero celular                | "999999999"                        | ALTA      |
| 497     | 497     | 1   | Alfa   | **Possui conjuge ou companheiro(a)?** | "N"=Nao / "S"=Sim — confirmado: mudou ao adicionar CPF do conjuge | ALTA |
| 498     | 505     | 8   | Num    | **Telefone fixo**             | "22429249" (2242-9249) / "        " (espacos) se vazio | ALTA |
| 506     | 871     | 366 | Alfa   | Campos adicionais/reserv      | (espacos + dados esparsos)         | BAIXA     |
| 872     | 872     | 1   | Num    | **Era residente no exterior e passou a ser residente no Brasil em 2025?** | "0"=Nao / "1"=Sim | ALTA |
| 873     | 880     | 8   | Data   | **Data de residencia no pais** | (vazio se Nao; "10112024" = 10/11/2024 formato DDMMAAAA) | ALTA |
| 881     | 920     | 40  | Alfa   | Campos adicionais/reserv      | (espacos + dados esparsos)         | BAIXA     |
| 921     | 930     | 10  | Num    | **Checksum**                  | "4068862196"                       | ALTA      |

**Evidencia - Variacao 01** (identif. contribuinte):
- Nome (pos 14-73): JORGE → MARIANA PROCOPIO SILVESTRE ✓
- Numero (pos 129-134): 260 → 340 ✓
- Complemento (pos 135-154): CASA → CASA TESTE ✓
- **Separador obrigatorio (pos 155)**: sempre ' ' (espaco) — NAO escrever bairro aqui!
- Bairro (pos 156-174, 19 chars): CENTRO → CENTRO TESTE ✓
- **ATENCAO**: escrita errada em pos 155 causa complemento="CASA C" e bairro="ENTRO" no IRPF
- Cod IBGE (pos 184-187): 5877 → 1697 (Petropolis → Jacana) ✓
- Municipio (pos 188-227): PETROPOLIS → JACANA ✓
- UF (pos 228-229): RJ → RN ✓
- Data nasc (pos 361-368): 10102000 → 11112011 ✓
- Flag identificacao (pos 391): N → S ✓
- DDD celular (pos 486-487): 24 → 25 (mudou)
- Celular (pos 488-496): 999999999 → 888888888 (mudou)
- Telefone fixo (pos 498-505): 22429249 (NAO mudou)
- DDD fixo (pos 348-349): 24 (NAO mudou — campo distinto do DDD celular pos 486-487)

**Evidencia - DDD fixo (348-349) e Telefone fixo (498-505)**:
- BASE (41653508000): DDD=[24], Tel=[22429249] — consistente com DDD Petropolis-RJ e numero residencial
- Todos os outros CPFs/arquivos (04, 05, 06, 12_saida): DDD=[  ], Tel=[        ] — sem telefone fixo cadastrado
- Confirmado: campo vazio usa ESPACOS (nao zeros)
- CEP (pos 175-182): 25845060 (NAO mudou)
- Logradouro (pos 89-128): AV KOELER (NAO mudou)

**Evidencia - Numero do recibo (analise comparativa entre arquivos)**:
- BASE (41653508000, transmitida): pos 444-453 = "4082548219", pos 454 = "A"
- Variacoes do mesmo CPF (rascunhos nao transmitidos): pos 444-453 = espacos, pos 454 = "A"
- 12_saida CPF 13103517750 (entregue): pos 444-453 = espacos, pos 454 = "S"
- Recibo exibido no PDF/programa: "408254821948" (12 digitos)
- **CONCLUSAO**: Os 10 digitos armazenados sao a BASE do recibo; os 2 ultimos digitos ("48")
  sao provavelmente digitos verificadores calculados pelo algoritmo do programa IRPF.
  Isso explica porque o campo "nao aceita qualquer numero" — os 2 ultimos sao validados.
- **NOTA**: A posicao 443 contem o ultimo digito do CNPJ da fonte pagadora ("2" de "27865757000102").
  O campo CNPJ completo dentro do bloco 392-443 ainda nao foi mapeado com precisao.

**Evidencia - Variacao 01_v2** (flags ID, CPF conjuge, data residencia, cor/raca → Indigena):
- **CPF conjuge (pos 337-347)**: espacos → "13103517760" ✓ (Reg16)
- **Houve alteracao de dados cadastrais? (pos 391)**: N → S ✓
- **Possui conjuge ou companheiro(a)? (pos 497)**: N → S ✓ (mudou junto com CPF do conjuge em pos 337-347)
- **Era residente no exterior? (pos 872)**: 0 → 1 ✓ (mudou junto com data de residencia em pos 873-880)
- **Flag resid. no pais (pos 872)**: "0" → "1" ✓ (marcado como residente)
- **Data residencia no pais (pos 873-880)**: espacos → "10112024" (10/11/2024) ✓
- **EFEITO COLATERAL**: pos 74-76 "RUA" → espacos (usuario limpou tipo logradouro)
- **EFEITO COLATERAL**: pos 382-386 mudou de "12002" para "   01" (campo desconhecido, possivelmente relacionado a natureza declaracao ou codigo conjuge — investigar)

---

## REGISTRO 19 - Resumo Rendimentos por Fonte - 346 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                         | Valor Encontrado     | Confianca |
|---------|---------|-----|------|-------------------------------|----------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                 | "19"                 | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte              | "41653508000"        | ALTA      |
| 14      | 27      | 14  | Num  | CNPJ fonte pagadora           | "27865757000102"     | ALTA      |
| 28      | 336     | 309 | Num  | Campos valores (13 digitos)   | Multiplos blocos     | MEDIA     |
| 337     | 346     | 10  | Num  | **Checksum**                  | "4282350985"         | ALTA      |

**Valores identificados no bloco 28-336** (blocos de 13 digitos cada):

| Bloco | Posicao    | Valor         | Valor R$      | Descricao provavel                     | Confianca |
|-------|------------|---------------|---------------|----------------------------------------|-----------|
| 1     | 28-40      | 0000000000000 | R$ 0,00       | **Imposto pago no exterior** (var11: R$500) | ALTA |
| 2     | 41-53      | 0000000000000 | R$ 0,00       | **Imposto complementar** (var11: R$200)     | ALTA |
| 3     | 54-66      | 0000000000000 | R$ 0,00       | ?                                      | BAIXA     |
| 4     | 67-79      | 0000018000000 | R$ 180.000,00 | **Total Exterior anual (soma Reg22 bloco4)** | ALTA      |
| 5     | 80-92      | 0000000000000 | R$ 0,00       | ?                                      | BAIXA     |
| 6     | 93-105     | 0000001800000 | R$ 18.000,00  | **Total Darf pago anual (soma Reg22 pos145-157)** | ALTA |
| 7-10  | 106-157    | 0000000000000 | R$ 0,00       | Campos zerados                         | BAIXA     |
| 11    | 158-170    | 0000001200000 | R$ 12.000,00  | ? (nao mudou na var03)                 | BAIXA     |
| 12    | 171-183    | 0000001200000 | R$ 12.000,00  | Contrib previdenciaria                 | ALTA      |
| 13    | 184-196    | 0000000000000 | R$ 0,00       | ?                                      | BAIXA     |
| 14    | 197-209    | 0000001200000 | R$ 12.000,00  | Contrib previdenciaria (confirmacao)   | ALTA      |
| 17    | 236-248    | 0000000000000 | R$ 0,00       | Total temporada+outros? (var03/02: 0009790000000 formato atipico) | BAIXA |
| 19    | 262-274    | 0018000000000 | ???           | Formato atipico - investigar           | BAIXA     |

**Evidencia - Variacao 03/02 (rendimentos PF/Exterior)**:
- Bloco 4: R$ 180.000 → R$ 42.600 = Total Exterior anual (print: 42.600 ✓)
- Bloco 6: R$ 18.000 → R$ 77.600 = Total Darf pago anual (print: 77.600 ✓)
- Blocos 12,14: R$ 12.000 → R$ 15.000 = Contrib previdenciaria (mesmo da var03)

**DESCOBERTA**: Reg 19 e um resumo anual derivado dos valores mensais do Reg 22.
Bloco 4 = soma dos 12 meses de Reg 22 bloco 4 (Exterior).
Bloco 6 = soma dos 12 meses de Reg 22 pos 145-157 (Darf pago).
NAO reflete valores do Reg 21 (rendimentos PJ) diretamente.

---

## REGISTRO 20 - Resumo de Calculos / Totais - 926 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                    | Valor Encontrado  | Confianca |
|---------|---------|-----|------|--------------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro            | "20"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte         | "41653508000"     | ALTA      |
| 14      | 916     | 903 | Num  | Blocos de valores (13d)  | Multiplos valores | MEDIA     |
| 917     | 926     | 10  | Num  | **Checksum**             | "2345898296"      | ALTA      |

**Campos confirmados via variacao 03** (blocos de 13 digitos a partir de pos 14):

| Bloco | Posicao  | Valor BASE    | Valor R$ BASE   | Valor VAR     | Descricao                          | Confianca |
|-------|----------|---------------|-----------------|---------------|------------------------------------|-----------|
| 1     | 14-26    | 0000018000000 | R$ 180.000,00   | 0000025000000 | Rendimentos tributaveis PJ         | ALTA      |
| 2     | 27-39    | 0000018000000 | R$ 180.000,00   | (nao mudou)   | Rendimentos (copia/outro?)         | MEDIA     |
| 5     | 66-78    | 0000036000000 | R$ 360.000,00   | 0000043000000 | Total rendimentos brutos           | ALTA      |
| 6     | 79-91    | 0000001200000 | R$ 12.000,00    | 0000001500000 | Deducoes previdencia               | ALTA      |
| 14    | 183-195  | 0000001816408 | R$ 18.164,08    | 0000002116408 | Parcela a deduzir tabela           | MEDIA     |
| 15    | 196-208  | 0000034183592 | R$ 341.835,92   | 0000040883592 | Base calculo (rend bruto - deducoes) | MEDIA   |
| 16    | 209-221  | 0000008315109 | R$ 83.151,09    | 0000010157609 | Imposto calculado tabela           | ALTA      |
| 18    | 235-247  | 0000008315109 | R$ 83.151,09    | 0000010157609 | Imposto calculado (copia 1)        | ALTA      |
| 20    | 261-273  | 0000008315109 | R$ 83.151,09    | 0000010157609 | Imposto calculado (copia 2)        | ALTA      |
| 21    | 274-286  | 0000008315109 | R$ 83.151,09    | 0000010157609 | Imposto calculado (copia 3)        | ALTA      |
| 22    | 287-299  | 0000005000000 | R$ 50.000,00    | 0000006000000 | Imposto retido na fonte            | ALTA      |
| 24    | 313-325  | 0000000000000 | R$ 0,00         | (var11: 0000000020000) | **Imposto complementar** (R$200) | ALTA |
| 25    | 326-338  | 0000000000000 | R$ 0,00         | (var11: 0000000050000) | **Imposto pago exterior** (R$500) | ALTA |
| 27    | 352-364  | 0000006835000 | R$ 68.350,00    | 0000007835000 | Total impostos pagos (acumulado)   | MEDIA     |
| 29    | 378-390  | 0000001480109 | R$ 14.801,09    | 0000002322609 | **Imposto a pagar (deducoes leg)** | ALTA      |
| 30    | 391-403  | 1000000148010 | cod+valor       | 1000000232260 | Cod tributacao + imposto a pagar   | MEDIA     |
| 36    | 469-481  | 0000000050000 | R$ 500,00       | (var03/03: 0000000080000) | Calculo derivado rend isento (R$500→R$800) | MEDIA |
| 37    | 482-494  | 0000000230000 | R$ 2.300,00     | 0000000260000 | ???                                | MEDIA     |
| 45    | 586-598  | 0000000050000 | R$ 500,00       | (var03/03: 0000000080000) | Calculo derivado rend isento (copia bloco 36) | MEDIA |
| 47    | 612-624  | 0000000230000 | R$ 2.300,00     | 0000000260000 | ??? (copia bloco 37)               | MEDIA     |
| 68    | 885-897  | 0023090000000 | 23,09%          | 0023620000000 | **Aliquota efetiva (deducoes leg)**| ALTA      |

**NOTA**: Bloco 5 (pos 66-78) = R$ 360.000 BASE, R$ 430.000 VAR. Diferenca = R$ 70k = mesma diferenca dos rendimentos.
Bloco 29 confirmado pelo print da variacao: "Imposto a Pagar: R$ 23.226,09".
Bloco 68 confirmado pelo print: "Aliquota efetiva: 23,62%".

---

## REGISTRO 21 - Rendimentos Tributaveis Recebidos de PJ - 170 caracteres

**CORRIGIDO via variacao 03**: Nao existem campos subtipo/versao e flag. CPF comeca direto na pos 3.
A ordem dos campos monetarios foi confirmada por cruzamento com print do programa IRPF.

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor BASE                            | Valor R$ BASE     | Confianca |
|---------|---------|-----|------|------------------------------|---------------------------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "21"                                  |                   | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                         |                   | ALTA      |
| 14      | 27      | 14  | Num  | CNPJ fonte pagadora          | "27865757000102"                      |                   | ALTA      |
| 28      | 87      | 60  | Alfa | Nome fonte pagadora          | "GLOBO COMUNICACAO E PARTICIPACOES S/A" |                 | ALTA      |
| 88      | 100     | 13  | Num  | Rendimentos recebidos (c)    | "0000018000000"                       | R$ 180.000,00     | ALTA      |
| 101     | 113     | 13  | Num  | Contrib previdenciaria (c)   | "0000001200000"                       | R$ 12.000,00      | ALTA      |
| 114     | 126     | 13  | Num  | **13o salario (c)**          | "0000001200000"                       | R$ 12.000,00      | ALTA      |
| 127     | 139     | 13  | Num  | **Imposto retido fonte (c)** | "0000005000000"                       | R$ 50.000,00      | ALTA      |
| 140     | 147     | 8   | Alfa | Reservado                    | (espacos)                             |                   | BAIXA     |
| 148     | 160     | 13  | Num  | IRRF sobre 13o salario (c)   | "0000000100000"                       | R$ 1.000,00       | ALTA      |
| 161     | 170     | 10  | Num  | **Checksum**                 | "0479665743"                          |                   | ALTA      |

**Evidencia - Variacao 03** (alteracao de rendimentos PJ no programa):
- Pos 88-100: R$ 180.000 -> R$ 250.000 (rendimentos) - Confirmado pelo print ✓
- Pos 101-113: R$ 12.000 -> R$ 15.000 (contrib prev) - Confirmado pelo print ✓
- Pos 114-126: R$ 12.000 -> R$ 15.000 (13o salario) - Confirmado pelo print ✓
- Pos 127-139: R$ 50.000 -> R$ 60.000 (IR retido) - Confirmado pelo print ✓
- Pos 148-160: R$ 1.000 -> R$ 2.000 (IRRF 13o) - Confirmado pelo usuario ✓

**ATENCAO**: A ordem no arquivo e: Rendimentos > Contrib Prev > 13o Salario > IR Retido Fonte.
Isso difere da tela do programa que mostra: Rendimentos > Contrib Prev > IR Retido > 13o Salario.

---

## REGISTRO 22 - Rendimentos Mensais PF/Exterior - 167 caracteres (12 registros, jan-dez)

**ATUALIZADO via variacao 03/02 (rendimentos PF/Exterior)**. Todos os blocos mapeados.

| Pos Ini | Pos Fim | Tam | Tipo | Campo                         | Valor BASE          | Confianca |
|---------|---------|-----|------|-------------------------------|---------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                 | "22"                | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte              | "41653508000"       | ALTA      |
| 14      | 14      | 1   | Alfa | Flag (N/S)                    | "N"                 | BAIXA     |
| 15      | 25      | 11  | Alfa | Reservado                     | (espacos)           | BAIXA     |
| 26      | 27      | 2   | Num  | Mes referencia (01-12)        | "01" a "12"         | ALTA      |
| 28      | 40      | 13  | Num  | Rend nao assalariado (c)      | "0000000000000"     | ALTA      |
| 41      | 53      | 13  | Num  | Aliquota incl temporada (c)   | "0000000000000"     | ALTA      |
| 54      | 66      | 13  | Num  | Outros rendimentos (c)        | "0000000000000"     | ALTA      |
| 67      | 79      | 13  | Num  | Exterior (c)                  | "0000001500000"     | ALTA      |
| 80      | 92      | 13  | Num  | Previdencia (c)               | "0000000000000"     | ALTA      |
| 93      | 105     | 13  | Num  | Qtd dependentes               | "0000000000000"     | MEDIA     |
| 106     | 118     | 13  | Num  | Pensao alimenticia (c)        | "0000000000000"     | ALTA      |
| 119     | 131     | 13  | Num  | Livro caixa (c)               | "0000000000000"     | ALTA      |
| 132     | 144     | 13  | Num  | **Total rendimentos mes (c)** | "0000001500000"     | ALTA      |
| 145     | 157     | 13  | Num  | **Darf pago cod 0190 (c)**    | "0000000150000"     | ALTA      |
| 158     | 167     | 10  | Num  | **Checksum**                  | (variavel)          | ALTA      |

**Evidencia - Variacao 03/02** (rendimentos PF/Exterior):
Cruzamento com print confirmou todos os blocos. Valores JAN:
- Bloco 2 (pos 41-53): `0000000100000` = R$ 1.000 ✓ Temporada
- Bloco 3 (pos 54-66): `0000000110000` = R$ 1.100 ✓ Outros
- Bloco 4 (pos 67-79): `0000000300000` = R$ 3.000 ✓ Exterior
- Bloco 9 (pos 132-144): `0000000510000` = R$ 5.100 = soma(1k+1.1k+3k) ✓ Total
- Pos 145-157: `0000000500000` = R$ 5.000 ✓ Darf pago

**Valores BASE**: R$ 15.000/mes no bloco Exterior. Darf BASE = R$ 1.500/mes.
12 x R$ 15.000 = R$ 180.000 (soma anual Exterior → Reg 19 bloco 4).
12 x R$ 1.500 = R$ 18.000 (soma anual Darf → Reg 19 bloco 6).

**Valores completos var 03/02** (rendimentos do exterior por mes):

| Mes | Temporada (B2) | Outros (B3) | Exterior (B4) | Total (B9) | Darf (145-157) |
|-----|----------------|-------------|---------------|------------|----------------|
| JAN | R$ 1.000       | R$ 1.100    | R$ 3.000      | R$ 5.100   | R$ 5.000       |
| FEV | R$ 2.000       | R$ 1.200    | R$ 3.100      | R$ 6.300   | R$ 6.100       |
| MAR | R$ 3.000       | R$ 1.300    | R$ 3.200      | R$ 7.500   | R$ 6.200       |
| ABR | R$ 4.000       | R$ 1.400    | R$ 3.300      | R$ 8.700   | R$ 6.300       |
| MAI | R$ 5.000       | R$ 1.500    | R$ 3.400      | R$ 9.900   | R$ 6.400       |
| JUN | R$ 6.000       | R$ 1.600    | R$ 3.500      | R$ 11.100  | R$ 6.500       |
| JUL | R$ 7.000       | R$ 1.700    | R$ 3.600      | R$ 12.300  | R$ 6.600       |
| AGO | R$ 8.000       | R$ 1.800    | R$ 3.700      | R$ 13.500  | R$ 6.700       |
| SET | R$ 9.000       | R$ 1.900    | R$ 3.800      | R$ 14.700  | R$ 6.800       |
| OUT | R$ 10.000      | R$ 2.000    | R$ 3.900      | R$ 15.900  | R$ 6.900       |
| NOV | R$ 11.000      | R$ 2.100    | R$ 4.000      | R$ 17.100  | R$ 7.000       |
| DEZ | R$ 12.000      | R$ 2.300    | R$ 4.100      | R$ 18.400  | R$ 7.100       |
| **TOTAL** | **R$ 78.000** | **R$ 19.900** | **R$ 42.600** | **R$ 140.500** | **R$ 77.600** |

Totais anuais confirmados no Reg 19: Exterior=R$42.600 (bloco4), Darf=R$77.600 (bloco6).

---

## REGISTRO 23 - Imposto Pago / Codigo - 40 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                | Valor Encontrado  | Confianca |
|---------|---------|-----|------|----------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro        | "23"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte     | "41653508000"     | ALTA      |
| 14      | 17      | 4   | Num  | Codigo/sequencial    | "0001"            | MEDIA     |
| 18      | 30      | 13  | Num  | Valor (centavos)     | "0000000500000" = R$ 5.000,00 | ALTA      |
| 31      | 40      | 10  | Num  | **Checksum**         | "2292057427"      | ALTA      |

**CORRECAO**: Valor era `0000000500000` (R$ 5.000), nao `0000005000000` (R$ 50.000) como documentado antes.
**Evidencia var 03/03**: Cod 0001 mudou R$ 5.000 → R$ 8.000 junto com rendimento isento no Reg 84.

---

## REGISTRO 24 - Deducoes Legais - 40 caracteres (3 registros)

| Pos Ini | Pos Fim | Tam | Tipo | Campo                | Valor Encontrado  | Confianca |
|---------|---------|-----|------|----------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro        | "24"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte     | "41653508000"     | ALTA      |
| 14      | 17      | 4   | Num  | Codigo deducao       | "0001/0006/0007"  | MEDIA     |
| 18      | 30      | 13  | Num  | Valor (centavos)     | Ver abaixo        | MEDIA     |
| 31      | 40      | 10  | Num  | **Checksum**         | (variavel)        | ALTA      |

**Registros encontrados**:
- Cod 0001: 0000001200000 = R$ 12.000,00 - **Previdencia oficial** (ALTA - confirmado via var03: mudou para R$ 15.000)
- Cod 0006: 0000001000000 = R$ 10.000,00 - **Tributacao exclusiva/definitiva** (ALTA - confirmado var03/04: mudou junto com Reg 88)
- Cod 0007: 0000000100000 = R$ 1.000,00 - **Rendimentos recebidos acumuladamente (RRA)** (ALTA - confirmado var03/05: mudou R$1k→R$1,9k junto com RRA)

---

## REGISTRO 25 - Dependentes - 224 caracteres

**CORRIGIDO v2 (variacao 04_dependente)**: Campos faltantes identificados entre CPF dependente e email.
Confirmado campo "Mora com titular" em pos 112. Gap 100-111 documentado como desconhecido.
Telefone confirmado como DDD(2)+Celular(9) = 11 chars (pos 203-213). Pos 214 = constante "2" (tipo tel?).

| Pos Ini | Pos Fim | Tam | Tipo | Campo                      | Valor Encontrado                            | Confianca |
|---------|---------|-----|------|----------------------------|---------------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro              | "25"                                        | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte           | "41653508000" / "12813061182"               | ALTA      |
| 14      | 18      | 5   | Num  | Sequencial dependente      | "00001" (1o dep)                            | ALTA      |
| 19      | 20      | 2   | Num  | Tipo dependente            | "21" (filho/enteado ate 21 anos)            | ALTA      |
| 21      | 80      | 60  | Alfa | Nome dependente            | "RYAN SILVA MONTANO" / "RAMESH JAYAPRAKASH" | ALTA      |
| 81      | 88      | 8   | Data | Data nascimento (DDMMAAAA) | "15032021" / "02021964"                     | ALTA      |
| 89      | 99      | 11  | Num  | CPF dependente             | "13480293077" / "06148283760"               | ALTA      |
| 100     | 111     | 12  | ???  | Desconhecido               | "            " (espacos em ambos)           | BAIXA     |
| 112     | 112     | 1   | Num  | Mora com o titular         | "0"=nao / "1"=sim                           | ALTA      |
| 113     | 202     | 90  | Alfa | Email                      | "TESTEDEPENDENTE@GMAIL.COM" (padded)        | ALTA      |
| 203     | 204     | 2   | Num  | DDD celular                | "22" / "  " (espacos) se vazio              | ALTA      |
| 205     | 213     | 9   | Num  | Celular (sem hifen)        | "989898989" / "         " (espacos) se vazio | ALTA     |
| 214     | 214     | 1   | Num  | **Flag celular preenchido**| "2"=com telefone / "0"=sem telefone (espacos) | ALTA   |
| 215     | 224     | 10  | Num  | **Checksum**               | "0063170408" / "2296329247"                 | ALTA      |

**Evidencia**: Confirmado via variacao 04_dependente. Dependente alterado de RYAN SILVA MONTANO
(CPF 13480293077, nasc 15/03/2021) para RAMESH JAYAPRAKASH (CPF 061.482.837-60, nasc 02/02/1964),
tipo 21, mora=1, email TESTEDEPENDENTE@GMAIL.COM, DDD 22, cel 98989-8989.
Campo "mora com titular" (pos 112) estava AUSENTE na documentacao anterior.

**Evidencia - analise BASE (dois dependentes)**:
- BASE tem 2 dependentes: dep1=RYAN (seq 00001, CPF 13480293077, nasc 15/03/2021, flag214=2)
  e dep2=FILHO TESTE 2 (seq 00002, sem CPF, nasc 05/02/2023, flag214=0, sem email/tel)
- FILHO TESTE 2 na BASE tem DDD+celular como espacos (pos 203-213) → flag214=0
- FILHO TESTE 2 na var04 tem DDD+celular como zeros (pos 203-213) → flag214=2 (artefato da variacao, nao padrao)
- **CONCLUSAO pos 214**: "2"=telefone preenchido com numero real; "0"=sem telefone (espacos)
- **PADRAO CORRETO**: campos DDD+celular vazios devem ser preenchidos com ESPACOS, nao zeros

---

## REGISTRO 26 - Pagamentos Efetuados - 671 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor Encontrado                | Confianca |
|---------|---------|-----|------|------------------------------|---------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "26"                            | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                   | ALTA      |
| 14      | 14      | 1   | Num  | Flag titular/depend          | "1"                             | MEDIA     |
| 15      | 19      | 5   | Num  | Codigo pagamento             | "00000"                         | MEDIA     |
| 20      | 30      | 11  | Num  | CPF/CNPJ beneficiario        | "66313835018"                   | ALTA      |
| 31      | 33      | 3   | Alfa | Reservado                    | "   "                           | BAIXA     |
| 34      | 93      | 60  | Alfa | Nome beneficiario            | "EZEQUIEL LUIS MENDONCA"        | ALTA      |
| 94      | 105     | 12  | Alfa | Reservado                    | (espacos)                       | BAIXA     |
| 106     | 118     | 13  | Num  | Valor pago (centavos)        | "0000000500000" = R$ 5.000,00   | ALTA      |
| 119     | 131     | 13  | Num  | Parcela nao dedutivel/reemb  | "0000000111100" = R$ 1.111,00   | ALTA      |
| 132     | 144     | 13  | Num  | Valor dedutivel              | "0000000000000"                 | MEDIA     |
| 145     | 145     | 1   | Num  | Sequencial                   | "1"                             | BAIXA     |
| 146     | 146     | 1   | Alfa | Tipo (T=titular?)            | "T"                             | MEDIA     |
| 147     | 661     | 515 | Alfa | Descricao / historico        | "PAGAMENTO DE MEDICO"           | ALTA      |
| 662     | 671     | 10  | Num  | **Checksum**                 | "1554688970"                    | ALTA      |

---

## REGISTRO 27 - Bens e Direitos - tamanho variavel (~1075-1091 chars conforme tipo de bem)

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                       | Valor Encontrado                | Confianca |
|---------|---------|-----|--------|-----------------------------|---------------------------------|-----------|
| 1       | 2       | 2   | Num    | Tipo registro               | "27"                            | ALTA      |
| 3       | 13      | 11  | Num    | CPF contribuinte            | "41653508000"                   | ALTA      |
| 14      | 15      | 2   | Num    | Codigo item                 | "11" (Apartamento)              | ALTA      |
| 16      | 16      | 1   | Num    | Flag exterior               | "0"=Nacional, "1"=Exterior      | ALTA      |
| 17      | 19      | 3   | Num    | Codigo pais (BACEN)         | "105"=Brasil, "149"=Canada, "756"=Africa do Sul | ALTA |
| 20      | 519     | 500 | Alfa   | Descricao / discriminacao   | "APARTAMENTO PETROPOLIS"        | ALTA      |
| 520     | 531     | 12  | Alfa   | Reservado                   | (espacos)                       | BAIXA     |
| 532     | 544     | 13  | Num    | Valor em 31/12 anterior (c) | "0000010000000" = R$ 100.000,00 | ALTA      |
| 545     | 557     | 13  | Num    | Valor em 31/12 atual (c)    | "0000020000000" = R$ 200.000,00 | ALTA      |
| 558     | 597     | 40  | Alfa   | Logradouro do bem           | "AV KOELER"                     | ALTA      |
| 598     | 603     | 6   | Alfnum | Numero                      | "260   "                        | ALTA      |
| 604     | 643     | 40  | Alfa   | Complemento                 | "PREDIO"                        | ALTA      |
| 644     | 683     | 40  | Alfa   | Bairro                      | "CENTRO"                        | ALTA      |
| 684     | 691     | 8   | Num    | CEP do bem                  | "25840600"                      | ALTA      |
| 692     | 692     | 1   | Alfa   | Separador                   | " "                             | BAIXA     |
| 693     | 694     | 2   | Alfa   | UF                          | "RJ"                            | ALTA      |
| 695     | 698     | 4   | Num    | Cod municipio IBGE          | "5877"                          | ALTA      |
| 699     | 738     | 40  | Alfa   | Municipio                   | "PETROPOLIS"                    | ALTA      |
| 739     | 862     | 124 | Alfa   | Campos adicionais           | (diversos — campos bancarios e financeiros) | BAIXA |
| 863     | 866     | 4   | Num    | Agencia (CONFLITO*)         | "8452" — CONFLITO: var05_contacorrente indica agencia em 1023-1026 | MEDIA |
| 867     | 879     | 13  | Alfa   | Reservado/espacos           | (espacos)                       | BAIXA     |
| 880     | 880     | 1   | Num    | Digito verificador (CONFLITO*)| "8" — CONFLITO: var05_contacorrente indica DV em 1040 | MEDIA |
| 881     | 891     | 11  | Alfa   | Reservado                   | (espacos)                       | BAIXA     |
| 892     | 895     | 4   | Num    | Reservado                   | "0000" (constante)              | BAIXA     |
| 896     | 896     | 1   | Num    | Desconhecido                | "1" (imovel c/data), "5" (sem data?) | BAIXA |
| 897     | 904     | 8   | Num    | **Data de aquisicao**       | DDMMYYYY. BASE=05062025, CASA=10102025 | ALTA |
| 905     | 932     | 28  | Alfa   | Reservado                   | (espacos)                       | BAIXA     |
| 933     | 943     | 11  | Num    | RENAVAM (cond. grupo02/c01) | "12354654564" em veiculo; vazio nos demais | ALTA  |
| 944     | 956     | 13  | Alfnum | Numero da conta             | "2222333333444"                 | ALTA      |
| 957     | 1022    | 66  | Alfa   | Campos adicionais           | (espacos + dados esparsos)      | BAIXA     |
| 1023    | 1026    | 4   | Num    | Agencia (CONFLITO*)         | **[grupo 06]** "0001" — CONFLITO: campo anterior indica agencia em 863-866 | MEDIA |
| 1027    | 1039    | 13  | Alfa   | Campos adicionais           | **[grupo 06]** (espacos/zeros entre agencia e DV) | BAIXA |
| 1026    | 1038    | 13  | Num    | Aplic Fin. Renda ou Perda (CONFLITO*) | "0000008888800" — CONFLITO: grupo 06 usa faixa 1027-1101 para dados bancarios | MEDIA |
| 1040    | 1040    | 1   | Num    | DV conta (CONFLITO*)        | **[grupo 06]** "2" — CONFLITO: campo anterior indica DV em 880 | ALTA |
| 1039    | 1051    | 13  | Num    | Aplic Fin. Imposto pago Ext (CONFLITO*) | "0000009999900" — CONFLITO: grupo 06 usa pos 1040 para DV e 1042-1055 para CNPJ banco | MEDIA |
| 1041    | 1041    | 1   | Alfa   | Separador                   | **[grupo 06]** " " (espaco entre DV e CNPJ) | BAIXA |
| 1042    | 1055    | 14  | Num    | CNPJ instituicao financeira | **[grupo 06]** "29138344000143" (Nu Pagamentos S.A.) | ALTA |
| 1056    | 1085    | 30  | Alfa   | Campos adicionais / padding | (espacos e zeros)               | BAIXA     |
| 1086    | 1088    | 3   | Num    | Cod BACEN banco             | **[grupo 06]** "260" (Nu Pagamentos S.A.) | ALTA |
| 1089    | 1089    | 1   | Alfa   | Separador                   | **[grupo 06]** "T"              | BAIXA     |
| 1090    | 1100    | 11  | Num    | CPF titular (referencia)    | **[grupo 06]** "41653508000"    | ALTA      |
| 1101    | 1102    | 2   | Num    | **Codigo grupo**            | "01"=Imoveis, "06"=Depositos a vista e numerario | ALTA |
| 1103    | 1103    | 1   | Num    | Desconhecido                | "0" (constante?)                | BAIXA     |
| 1104    | 1112    | 9   | Alfnum | **Numero da conta bancaria**| **[grupo 06]** "104911308"      | ALTA      |
| 1113    | 1241    | 129 | Alfa   | Campos adicionais / padding | (espacos e zeros)               | BAIXA     |
| 1242    | 1251    | 10  | Num    | **Checksum**                | BASE="0820608248"               | ALTA      |

> **CONFLITO***: O registro 27 tem estrutura CONDICIONAL por tipo de bem. Os campos marcados
> com **[grupo 06]** so aparecem quando codigo grupo (pos 1101-1102) = "06" (Depositos a vista
> e numerario). Para outros grupos (imoveis, veiculos, aplicacoes financeiras etc.) as mesmas
> posicoes contem campos diferentes. Investigar qual variacao mapeou agencia/DV em 863-866/880.

**Evidencia - Variacao 05** (bens e direitos):
- Cod item (14-15): 11→12 (Apartamento→Casa) ✓
- Flag exterior (16): 0→1 (Nacional→Exterior) ✓ (confirmado em 2 bens distintos)
- Codigo pais (17-19): "105"→"149" (Brasil→Canada); "105"→"756" (Brasil→Africa do Sul) ✓
- Descricao (20-519): adicionou "TESTE" ✓
- Logradouro (558-597): "AV KOELER"→"AV KOELER 312" ✓
- Val 31/12 anterior (532-544) e atual (545-557) NAO mudaram (R$100k e R$200k)
- **CORRIGIDO**: pos 16 nao e subgrupo, e flag exterior; pos 17-19 e codigo pais completo (3 digitos)
- Codigo grupo (1101-1102): BASE=01 (Imovel), CONTA CORRENTE=06, TESTE=02, CASA=01 ✓
- Checksum corrigido para pos 1242-1251 (registro tem 1251 chars, nao 1091)
- Data de aquisicao (897-904): DDMMYYYY. CASA="10102025"=10/10/2025 ✓; BASE="05062025"; TESTE="00000000" (sem data)
- RENAVAM (933-943): "12354654564" no veiculo (grupo02/cod01); BASE e CASA vazios — campo condicional de veiculo ✓
- **CONTA CORRENTE NA NU PAGAMENTOS S.A.** (grupo 06, cod item 01):
  - Cod item (14-15): 01 (vs "11" do apartamento) ✓
  - Agencia (1023-1026): "0001" ✓
  - Padding agencia-DV (1027-1039): 13 chars ✓
  - DV (1040): "2" ✓  **[CORRIGIDO: era 1039, estava errado]**
  - Separador (1041): " " ✓
  - CNPJ banco (1042-1055): "29138344000143" (Nu Pagamentos S.A.) ✓  **[CORRIGIDO: era 1041-1054]**
  - Cod BACEN banco (1086-1088): "260" (Nu Pagamentos = banco 260) ✓  **[CORRIGIDO: era 1085-1087]**
  - Separador T (1089): "T" ✓  **[CORRIGIDO: era 1088]**
  - CPF titular referencia (1090-1100): "41653508000" ✓  **[CORRIGIDO: era 1089-1099]**
  - Numero conta (1104-1112): "104911308" ✓
  - Campos de endereco (558-738): todos espacos (nao se aplica a conta bancaria) ✓
  - **ATENCAO**: Posicoes DV/CNPJ/BACEN/T/CPF foram corrigidas — estavam todas 1 posicao adiantadas.
    Confirmado via comparacao direta com arquivo gerado pelo IRPF.

---

## REGISTRO 28 - Dividas e Onus Reais - 576 caracteres

**CORRIGIDO**: Nao existem campos subtipo/versao e flag. CPF comeca na pos 3. Posicoes monetarias recalculadas.

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor Encontrado               | Confianca |
|---------|---------|-----|------|------------------------------|--------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "28"                           | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                  | ALTA      |
| 14      | 15      | 2   | Num  | Codigo divida                | "11"                           | ALTA      |
| 16      | 515     | 500 | Alfa | Descricao / discriminacao    | "EMPRESTIMO PARA APARTAMENTO"  | ALTA      |
| 516     | 527     | 12  | Alfa | Reservado                    | (espacos)                      | BAIXA     |
| 528     | 540     | 13  | Num  | Saldo em 31/12 anterior (c)  | "0000015000000" = R$ 150.000,00 | MEDIA     |
| 541     | 553     | 13  | Num  | Saldo em 31/12 atual (c)     | "0000020000000" = R$ 200.000,00 | MEDIA     |
| 554     | 566     | 13  | Num  | Valor pago no ano (c)        | "0000002000000" = R$ 20.000,00  | MEDIA     |
| 567     | 576     | 10  | Num  | **Checksum**                 | "2925550781"                   | ALTA      |

---

## REGISTRO 45 - Rendimentos Isentos e Nao Tributaveis (PJ) - 216 caracteres

**ATUALIZADO via variacao 03/05** (RRA). Valores BASE corrigidos (eram R$15k, sao R$1,5k).
Reg 45 e um resumo POR FONTE que acumula rendimentos isentos + dados RRA.

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor BASE                            | Confianca |
|---------|---------|-----|------|------------------------------|---------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "45"                                  | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                         | ALTA      |
| 14      | 15      | 2   | Alfa | Reservado                    | "  "                                  | BAIXA     |
| 16      | 29      | 14  | Num  | CNPJ fonte pagadora          | "27865757000102"                      | ALTA      |
| 30      | 89      | 60  | Alfa | Nome fonte pagadora          | "GLOBO COMUNICACAO E PARTICIPACOES S/A" | ALTA    |
| 90      | 102     | 13  | Num  | Total rendimentos isentos (c)| "0000000150000" = R$ 1.500,00         | ALTA      |
| 103     | 115     | 13  | Num  | Total contrib prev (c)       | "0000000015000" = R$ 150,00           | ALTA      |
| 116     | 128     | 13  | Num  | ???                          | "0000000000000"                       | BAIXA     |
| 129     | 141     | 13  | Num  | Total imposto retido (c)     | "0000000035000" = R$ 350,00           | ALTA      |
| 142     | 143     | 2   | Num  | Mes recebimento RRA          | "01"                                  | ALTA      |
| 144     | 148     | 5   | Alfnum | Metadados RRA              | "00001"                               | MEDIA     |
| 149     | 149     | 1   | Alfa | Separador                    | " "                                   | BAIXA     |
| 150     | 152     | 3   | Num  | Flag/codigo                  | "100"                                 | BAIXA     |
| 153     | 153     | 1   | Num  | Num meses RRA                | "5"                                   | ALTA      |
| 154     | 154     | 1   | Num  | Reservado                    | "0"                                   | BAIXA     |
| 155     | 180     | 26  | Num  | Campos zerados               | (zeros)                               | BAIXA     |
| 181     | 193     | 13  | Num  | Total rend isentos (copia)   | "0000000150000" = R$ 1.500,00         | ALTA      |
| 194     | 206     | 13  | Num  | Reservado                    | "0000000000000"                       | BAIXA     |
| 207     | 216     | 10  | Num  | **Checksum**                 | "2025999635"                          | ALTA      |

**CORRECAO**: Valores BASE eram R$ 1.500 (nao R$ 15.000 como documentado antes).

**Evidencia - Variacao 03/05** (RRA - print: rend=R$2.500, prev=R$200, IR=R$400, mes=Fev, meses=4):
- B1 (90-102): R$ 1.500 → R$ 2.500 (total rend isentos acumulado) ✓
- B2 (103-115): R$ 150 → R$ 200 (total contrib prev acumulada) ✓
- B4 (129-141): R$ 350 → R$ 400 (total IR retido acumulado) ✓
- Pos 142-143: `01` → `02` = mes Fevereiro ✓
- Pos 153: `5` → `4` = numero de meses 4 ✓
- B8 (181-193): R$ 1.500 → R$ 2.500 (copia B1) ✓

---

## REGISTRO 84 - Rendimentos Isentos e Nao Tributaveis - 144 caracteres

**RECLASSIFICADO via variacao 03/03**: Este registro armazena rendimentos isentos (nao apenas carne-leao).
Print confirmou: "Editar: Rendimento Isento e Nao Tributavel", tipo 01 (Bolsas de estudo).

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor BASE                            | Confianca |
|---------|---------|-----|------|------------------------------|---------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "84"                                  | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                         | ALTA      |
| 14      | 14      | 1   | Alfa | Tipo beneficiario (T=titular)| "T"                                   | ALTA      |
| 15      | 25      | 11  | Num  | CPF beneficiario             | "41653508000"                         | ALTA      |
| 26      | 29      | 4   | Num  | Codigo tipo rendimento       | "0001" (01=Bolsas de estudo)          | ALTA      |
| 30      | 43      | 14  | Num  | CNPJ fonte pagadora          | "27865757000102"                      | ALTA      |
| 44      | 103     | 60  | Alfa | Nome fonte pagadora          | "GLOBO COMUNICACAO E PARTICIPACOES S/A" | ALTA    |
| 104     | 116     | 13  | Num  | **Valor rendimento isento (c)** | "0000000500000" = R$ 5.000,00      | ALTA      |
| 117     | 121     | 5   | Num  | Reservado/zeros              | "00000"                               | BAIXA     |
| 122     | 134     | 13  | Num  | Valor adicional              | "0000000000000"                       | BAIXA     |
| 135     | 144     | 10  | Num  | **Checksum**                 | "3479762237"                          | ALTA      |

**Evidencia - Variacao 03/03** (rendimentos isentos):
- Nome fonte (pos 44-103): adicionou "TESTE" apos "S/A" ✓
- Valor isento (pos 104-116): R$ 5.000 → R$ 8.000 - confirmado pelo print ✓
- Reg 23 cod 0001 mudou junto: R$ 5.000 → R$ 8.000 (total por codigo)
- Reg 20 blocos 36 e 45 (pos 469-481, 586-598): R$ 500 → R$ 800 (calculo derivado)
- Registro 45 (rendimentos isentos PJ) NAO foi afetado

---

## REGISTRO 88 - Rendimentos Sujeitos a Tributacao Exclusiva/Definitiva - 131 caracteres

**RECLASSIFICADO via variacao 03/04**: Este registro armazena rendimentos sujeitos a tributacao
exclusiva/definitiva (nao apenas imposto pago DARF como classificado antes).
Print confirmou: "Editar: Rendimento Sujeito a Tributacao Exclusiva/Definitiva", tipo 06 (aplicacoes financeiras).

| Pos Ini | Pos Fim | Tam | Tipo | Campo                              | Valor BASE                            | Confianca |
|---------|---------|-----|------|------------------------------------|---------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                      | "88"                                  | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte                   | "41653508000"                         | ALTA      |
| 14      | 14      | 1   | Alfa | Tipo beneficiario (T=titular)      | "T"                                   | ALTA      |
| 15      | 25      | 11  | Num  | CPF beneficiario                   | "41653508000"                         | ALTA      |
| 26      | 29      | 4   | Num  | Codigo tipo rendimento             | "0006" (06=Aplicacoes financeiras)    | ALTA      |
| 30      | 43      | 14  | Num  | CNPJ fonte pagadora                | "27865757000102"                      | ALTA      |
| 44      | 103     | 60  | Alfa | Nome fonte pagadora                | "GLOBO COMUNICACAO E PARTICIPACOES S/A" | ALTA    |
| 104     | 116     | 13  | Num  | **Valor rendimento (c)**           | "0000001000000" = R$ 10.000,00        | ALTA      |
| 117     | 121     | 5   | Num  | Reservado/zeros                    | "00000"                               | BAIXA     |
| 122     | 131     | 10  | Num  | **Checksum**                       | "2698011732"                          | ALTA      |

**Evidencia - Variacao 03/04** (trib exclusivo):
- Nome fonte (pos 44-103): adicionou "123" apos "S/A" ✓
- Valor (pos 104-116): R$ 10.000 → R$ 15.000 - confirmado pelo print ✓
- Reg 24 cod 0006 mudou junto: R$ 10.000 → R$ 15.000 (mesmo valor)
- Reg 20 blocos 37,47 (pos 482-494, 612-624): R$ 2.300 → R$ 3.100 (calculo derivado)

**NOTA**: Estrutura identica ao Registro 84 (rendimentos isentos). Ambos usam:
Tipo(2) + CPF(11) + TipoBenef(1) + CPF(11) + Cod(4) + CNPJ(14) + Nome(60) + Valor(13) + Zeros(5) + ValAdicional(13) + Checksum(10)

---

## REGISTRO 39 - Declaracao de Saida Definitiva - 193 caracteres

**Descoberto via variacao 12_saida** - Registro exclusivo de declaracoes de saida definitiva (IRPF-S).
Presente apenas quando o contribuinte informa saida do pais.

| Pos Ini | Pos Fim | Tam | Tipo | Campo                              | Valor Encontrado              | Confianca |
|---------|---------|-----|------|------------------------------------|-------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                      | "39"                          | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte                   | "13103517750"                 | ALTA      |
| 14      | 24      | 11  | Num  | **CPF do procurador**              | "98736521263"                 | ALTA      |
| 25      | 84      | 60  | Alfa | **Nome do procurador**             | "NOME DO PROCURADOR"          | ALTA      |
| 85      | 164     | 80  | Alfa | **Endereco do procurador**         | "ENDERECO DO PROCURADO PARA TESTE" | ALTA |
| 165     | 172     | 8   | Data | **Data nao residente (ddmmaaaa)**  | "10102025" (10/10/2025)       | ALTA      |
| 173     | 180     | 8   | Data | **Data residente no pais (ddmmaaaa)** | "09092025" (09/09/2025)    | ALTA      |
| 181     | 183     | 3   | Num  | **Codigo pais destino**            | "149" (Canada)                | ALTA      |
| 184     | 193     | 10  | Num  | **Checksum**                       | "0617626333"                  | ALTA      |

**Evidencia - Variacao 12_saida** (arquivo Receita: 13103517750):
- CPF procurador (pos 14-24): "98736521263" = 987.365.212-63 do print ✓
- Nome procurador (pos 25-84): "NOME DO PROCURADOR" ✓
- Endereco procurador (pos 85-164): "ENDERECO DO PROCURADO PARA TESTE" ✓
- Data nao residente (pos 165-172): "10102025" = 10/10/2025 ✓
- Data residente (pos 173-180): "09092025" = 09/09/2025 ✓
- Pais destino (pos 181-183): "149" = Canada ✓
- **NOTA**: Formato data aqui e DDMMAAAA, diferente do header IRPF que usa AAAAMMDD para data de saida

**BUG identificado - Arquivo gerado pelo nosso sistema (71930207140)**:
- CPF procurador (pos 14-24): preenchido com CPF do proprio contribuinte ("71930207140") em vez do CPF do procurador
- Nome procurador (pos 25-84): vazio (espacos) — nao preenchido
- Endereco procurador (pos 85-164): vazio (espacos) — nao preenchido
- Data residente (pos 173-180): "00000000" (nao informada)
- **CONCLUSAO**: GeradorSaidaDefinitiva nao esta mapeando corretamente os campos do procurador

**Convencao nome arquivo**:
- `IRPF-A` = Declaracao de Ajuste Anual
- `IRPF-S` = Declaracao de Saida Definitiva do Pais

---

## REGISTRO T9 - Trailer / Totalizador - 449 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor Encontrado  | Confianca |
|---------|---------|-----|------|------------------------------|-------------------|-----------|
| 1       | 2       | 2   | Alfa | Tipo registro                | "T9"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"     | ALTA      |
| 14      | 21      | 8   | Num  | Total de registros           | "00000028" (28)   | ALTA      |
| 22      | 439     | 418 | Num  | Contadores por tipo registro | Ver abaixo        | MEDIA     |
| 440     | 449     | 10  | Num  | **Checksum**                 | "4240261545"      | ALTA      |

**Contadores identificados** (blocos de 5 digitos = qtd registros por tipo):

| Posicao | Valor   | Interpretacao                   |
|---------|---------|---------------------------------|
| 22-26   | "00001" | 1 registro tipo IRPF (header)   |
| 27-31   | "00000" | 0 registros tipo ??             |
| 32-36   | "00000" | 0 registros                     |
| 37-41   | "00001" | 1 registro tipo 16              |
| 42-46   | "00001" | 1 registro tipo 19              |
| 47-51   | "00010" | Não - provavelmente 00001       |
| 52-56   | "00012" | 12 registros tipo 22            |
| 57-61   | "00001" | 1 registro tipo 23?             |
| 62-66   | "00003" | 3 registros tipo 24             |
| 67-71   | "00001" | 1 registro tipo 25              |
| 72-76   | "00001" | 1 registro tipo 26              |
| 77-81   | "00001" | 1 registro tipo 27              |
| 82-86   | "00001" | 1 registro tipo 28              |

---

## Observacoes e Padroes Gerais

### Estrutura comum de todos os registros:
```
[Tipo 2-4 chars][CPF 11 chars][...campos...][Checksum 10 chars]
```

**IMPORTANTE**: Registros 21, 25 e 28 NAO possuem campos "subtipo/versao" e "flag" entre
o tipo e o CPF. O CPF comeca direto na posicao 3 (apos o tipo de 2 chars). Os primeiros
digitos do CPF ("41") foram erroneamente interpretados como subtipo="4" e flag="1" na
analise inicial. Isso deslocava todas as posicoes subsequentes em +2.

### Convencoes de valores monetarios:
- Formato: 13 digitos numericos com zeros a esquerda
- **CONFIRMADO**: Valores em centavos (ultimos 2 digitos implicitos = casas decimais)
- Exemplo: "0000018000000" = 18.000.000 centavos = **R$ 180.000,00**
- Exemplo: "0000001200000" = 1.200.000 centavos = **R$ 12.000,00**
- Exemplo: "0000000100000" = 100.000 centavos = **R$ 1.000,00**
- Evidencia: Variacao 03 - usuario confirmou BASE rendimentos = R$ 180.000,00, valor no arquivo = "0000018000000"

### Checksums:
- Ultimos 10 digitos de cada linha
- Algoritmo desconhecido (possivelmente modulo ou CRC)
- Muda completamente com qualquer alteracao no registro

### Campos de texto:
- Preenchidos com espacos a direita (right-padded)
- Sem acentuacao (ASCII puro)
- Maiusculas

---

## Status do Mapeamento

| Registro | Campos identificados | Confianca geral | Necessita variacao |
|----------|---------------------|-----------------|--------------------|
| IRPF     | ~70%                | ALTA            | SIM (campos reservados) |
| 16       | ~80%                | **ALTA**        | SIM (CNPJ dentro de 392-443 e pos 455-485 e pos 506-920 desconhecidos) |
| 19       | ~50%                | ALTA            | SIM (blocos 4,6,12,14 confirmados via var03/02) |
| 20       | ~40%                | MEDIA           | SIM (17 blocos confirmados via var03) |
| 21       | **100%**            | **ALTA**        | **COMPLETO** (confirmado via var03) |
| 22       | **~95%**            | **ALTA**        | **COMPLETO** (9 blocos + darf mapeados via var03/02) |
| 23       | ~80%                | ALTA            | SIM (cod 0001 confirmado via var03/03) |
| 24       | ~80%                | ALTA            | SIM (cod 0001 confirmado via var03) |
| 25       | ~80%                | ALTA            | SIM (campos extras) |
| 26       | ~85%                | **ALTA**        | **Val pago, reembolso, descricao confirmados (var06)** |
| 27       | **~85%**            | **ALTA**        | **Posicoes corrigidas, campos endereco confirmados (var05)** |
| 28       | ~60%                | MEDIA           | SIM                |
| 45       | **~80%**            | **ALTA**        | **Mapeado via var03/05: totais por fonte + metadados RRA** |
| 84       | **~90%**            | **ALTA**        | **Reclassificado: Rend Isentos (confirmado var03/03)** |
| 39       | **100%**            | **ALTA**        | **COMPLETO** (mapeado via var12_saida) |
| 88       | **~90%**            | **ALTA**        | **Reclassificado: Trib Exclusiva (confirmado var03/04)** |
| T9       | ~50%                | MEDIA           | SIM                |

---

## Proximos Passos - Metodo de Variacao Controlada

Para aumentar a confianca do mapeamento, seguir o protocolo:

### Variacoes realizadas:

| # | Variacao | Status | Descobertas principais |
|---|----------|--------|------------------------|
| 01 | **Alterar identif. contribuinte** | **CONCLUIDA** | **Nome, numero, complemento, bairro, UF, municipio, IBGE, nasc confirmados em Reg 16 e IRPF. Flag alt cadastral (pos 391). DDD+celular (pos 486-496). Telefone fixo (pos 498-505). Email corrigido para pos 236-295. NOTA: tambem inclui mudancas de rendimentos (mesmas da var03).** |
| 02 | Alterar endereco | CONCLUIDA | Campos endereco nos registros 16 e IRPF |
| 03/01 | **Alterar rendimento PJ** | **CONCLUIDA** | **Escala monetaria confirmada (centavos). Registro 21 100% mapeado. Posicoes corrigidas (-2). Ordem campos: Rend > Prev > 13o > IR. Registro 20: 17 blocos identificados. Reg 24 cod 0001 confirmado.** |
| 03/02 | **Rendimentos PF/Exterior** | **CONCLUIDA** | **Registro 22: 9 blocos + Darf mapeados. Registro 19: blocos 4 e 6 = totais anuais de Exterior e Darf (derivados do Reg 22). NOTA: tambem inclui mudancas de var01 e var03/01.** |
| 03/03 | **Rendimentos isentos** | **CONCLUIDA** | **Registro 84 reclassificado: armazena rendimentos isentos (nao carne-leao). Valor isento pos 104-116 confirmado (R$5k→R$8k). Reg 23 cod 0001 = mesmo valor. Reg 45 NAO afetado. Reg 20 blocos 36,45 novos.** |
| 03/04 | **Trib exclusiva/definitiva** | **CONCLUIDA** | **Registro 88 reclassificado: armazena rend trib exclusiva (nao DARF). Cod 0006=aplicacoes financeiras. Valor pos 104-116 (R$10k→R$15k). Reg 24 cod 0006 = mesmo valor (nao e pensao). Estrutura identica ao Reg 84.** |
| 03/05 | **Rend recebidos acumuladamente** | **CONCLUIDA** | **Reg 45 mudou pela 1a vez! Armazena totais por fonte + metadados RRA (mes pos 142-143, num meses pos 153). Valores BASE corrigidos (R$1,5k nao R$15k). Reg 24 cod 0007 = RRA (R$1k→R$1,9k).** |
| 11 | **Imposto pago/retido** | **CONCLUIDA** | **Sem novos registros. Dados vao para Reg 19 (blocos 1,2 = imp exterior/complementar) e Reg 20 (blocos 24,25). Tela de resumo apenas afeta calculos.** |
| 05 | **Bens e direitos** | **CONCLUIDA** | **Reg 27: posicoes corrigidas. Val anterior pos 532-544, val atual pos 545-557, logradouro pos 558-597, numero 598-603, compl 604-643, bairro 644-683, CEP 684-691, UF 693-694, IBGE 695-698, municipio 699-738. Cod item pos 14-15. CORRIGIDO: pos 16=flag exterior (0/1), pos 17-19=cod pais 3 digitos (105=Brasil, 149=Canada, 756=Africa do Sul). Grupo pos 1101-1102 (ALTA). Checksum corrigido para 1242-1251 (registro 1251 chars). CONTA CORRENTE (grupo=06, cod=01): agencia 1023-1026, DV 1039, CNPJ banco 1041-1054, cod BACEN 1085-1087, CPF titular 1089-1099, num conta 1104-1112. CONFLITO: agencia/DV mapeados anteriormente em 863-866/880 — provavel outro subtipo de bem.** |
| 06 | **Pagamento efetuado** | **CONCLUIDA** | **Reg 26: valor pago pos 106-118 (R$5k→R$9k), reembolso pos 119-131 (R$1.111→R$1.544), descricao pos 147+ confirmados. Campos val pago e reembolso = ALTA.** |
| 04 | **Corrigir mapeamento dependente** | **CONCLUIDA** | **Reg 25: campo "Mora com titular" descoberto em pos 112 (estava AUSENTE). Gap 100-111 documentado como desconhecido (12 chars). DDD(203-204)+Cel(205-213) separados. Pos 214="2" constante (tipo tel?). CPF, nasc, nome, email todos confirmados.** |
| 05 | Adicionar bem | CONCLUIDA | Estrutura registro 27 |
| 06 | Remover pagamento | CONCLUIDA | Campos registro 26 |
| 07 | Alterar estado civil | CONCLUIDA | Flags no header e registro 16 |
| 08 | Alterar CEP | CONCLUIDA | Campos CEP/municipio |
| 09 | Alterar divida | CONCLUIDA | Campos registro 28 |
| 10 | **Corrigir ocupacao** | **CONCLUIDA** | **Reg16 pos 382-384 = Ocupacao principal (3 chars), pos 385-386 = Natureza da ocupacao (2 chars). Confirmado com natureza=02/ocup=120 (NOVO) vs natureza=01/ocup=000 (BASE). IRPF header pos 36-39 "1100" = CONSTANTE, NAO e ocupacao. Reservado 369-381 corrigido para 13 chars.** |
| 12 | **Declaracao de saida** | **CONCLUIDA** | **Novo registro tipo 39 (193 chars) com dados de saida: CPF/nome/endereco procurador, data nao residente, data residente, pais destino. Header IRPF pos 19-20: "00"=Ajuste, "20"=Saida. Header pos 673-680: data saida (AAAAMMDD). Header pos 681: flag procurador. Header pos 682-692: CPF procurador. Filename: IRPF-A (Ajuste) vs IRPF-S (Saida).** |

### Proximas variacoes recomendadas:

1. **Alterar SOMENTE 13o salario** -> Confirma pos 114-126 isoladamente (atualmente confirmado junto com rendimentos)
2. **Alterar rendimentos mensais (Reg 22)** -> Confirma relacao com Reg 19 bloco 4 e Reg 20 bloco 5
3. **Adicionar segunda fonte pagadora** -> Confirma multiplicidade de Reg 19/21
4. **Alterar valor pagamento medico** -> Confirma campos monetarios Reg 26 e efeito no Reg 20

### Para cada variacao:
1. Gerar novo .DBK e PDF no programa IRPF
2. Executar script de comparacao (diff posicional)
3. Correlacionar com mudanca no PDF
4. Atualizar esta documentacao
