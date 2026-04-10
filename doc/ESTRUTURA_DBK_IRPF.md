# Engenharia Reversa - Arquivo .DBK IRPF 2026/2025

## Visao Geral

- **Arquivo base**: `41653508000-IRPF-A-2026-2025-ORIGI.DBK`
- **Formato**: Texto posicional (largura fixa), 1 registro por linha
- **Encoding**: ASCII/Latin-1
- **Valores monetarios**: Inteiros em centavos, 13 digitos com zeros a esquerda. Ex: `0000018000000` = R$ 180.000,00
- **Checksum**: Ultimos 10 digitos de cada linha
- **Convencao nome arquivo**: `IRPF-A` = Ajuste Anual / `IRPF-S` = Saida Definitiva

---

## Tipos de Registro Identificados

| Tipo | Descricao                                  | Qtd | Tamanho (chars) |
|------|--------------------------------------------|-----|-----------------|
| IRPF | Header / Cabecalho da declaracao           | 1   | 1244            |
| 16   | Dados pessoais do contribuinte             | 1   | 930             |
| 19   | Resumo rendimentos por fonte               | 1   | 346             |
| 20   | Resumo calculos / totais                   | 1   | 926             |
| 21   | Rendimentos tributaveis recebidos de PJ    | 1   | 170             |
| 22   | Rendimentos mensais PF/Exterior (carne-leao) | 12 | 167             |
| 23   | Imposto pago / retido por codigo           | 1   | 40              |
| 24   | Deducoes legais por codigo                 | 3   | 40              |
| 25   | Dependentes                                | 1   | 224             |
| 26   | Pagamentos efetuados                       | 1   | 671             |
| 27   | Bens e direitos                            | 1   | 1251            |
| 28   | Dividas e onus reais                       | 1   | 576             |
| 39   | Declaracao de saida definitiva             | 0-1 | 193             |
| 45   | Rendimentos Recebidos Acumuladamente (RRA) | 0-1 | 216             |
| 80   | Rendimento com exigibilidade suspensa      | 0-N | 123             |
| 84   | Rendimento isento/nao tributavel (1 por lancamento) | 0-N | 144    |
| 88   | Rendimento sujeito a tributacao exclusiva/definitiva (1 por lancamento) | 0-N | 131 |
| T9   | Trailer / Totalizador                      | 1   | 449             |

---

## REGISTRO: IRPF (Header) - 1244 caracteres

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                          | Valor Encontrado                                      | Confianca |
|---------|---------|-----|--------|--------------------------------|-------------------------------------------------------|-----------|
| 1       | 4       | 4   | Alfa   | Identificador sistema          | "IRPF"                                                | ALTA      |
| 5       | 8       | 4   | Alfa   | Reservado                      | "    "                                                | MEDIA     |
| 9       | 12      | 4   | Num    | Ano exercicio                  | "2026"                                                | ALTA      |
| 13      | 16      | 4   | Num    | Ano calendario (base)          | "2025"                                                | ALTA      |
| 17      | 18      | 2   | Num    | Codigo/versao                  | "36"                                                  | MEDIA     |
| 19      | 20      | 2   | Num    | **Tipo declaracao**            | "00"=Ajuste Anual / "20"=Saida Definitiva             | ALTA      |
| 21      | 21      | 1   | Alfa   | **Separador tipo declaracao**  | "0"=Ajuste / " "=Saida — alterar sempre junto com pos 19-20 | ALTA |
| 22      | 32      | 11  | Num    | CPF do contribuinte            | "41653508000"                                         | ALTA      |
| 33      | 35      | 3   | Alfa   | Reservado                      | "   "                                                 | BAIXA     |
| 36      | 39      | 4   | Num    | Codigo ocupacao                | "1100"                                                | MEDIA     |
| 40      | 99      | 60  | Alfa   | Nome completo                  | "JORGE LUCAS DA SILVA MONTANO"                        | ALTA      |
| 100     | 101     | 2   | Alfa   | UF                             | "RJ"                                                  | ALTA      |
| 102     | 112     | 11  | Num    | Hash calculado                 | Recalculado a cada alteracao de dados                 | BAIXA     |
| 113     | 120     | 8   | Data   | Data nascimento (ddmmaaaa)     | "10102000"                                            | ALTA      |
| 121     | 121     | 1   | Alfa   | Estado civil (S/C/D/V)         | "S"                                                   | ALTA      |
| 122     | 122     | 1   | Num    | Tipo declaracao                | "1"                                                   | BAIXA     |
| 123     | 123     | 1   | Alfa   | Flag (S/N)                     | "S"                                                   | BAIXA     |
| 124     | 134     | 11  | Alfa   | Reservado                      | (espacos)                                             | BAIXA     |
| 135     | 148     | 14  | Alfa   | Sistema operacional            | "WINDOWS 11   "                                       | ALTA      |
| 149     | 154     | 6   | Alfa   | Versao SO                      | "10.0  "                                              | ALTA      |
| 155     | 166     | 12  | Alfa   | Versao programa IRPF           | " 17.0.16   "                                         | ALTA      |
| 167     | 174     | 8   | Alfa   | Reservado                      | (espacos)                                             | BAIXA     |
| 175     | 178     | 4   | Num    | Cod municipio (IBGE)           | "5877"                                                | ALTA      |
| 179     | 189     | 11  | Num    | CPF do conjuge/companheiro     | (espacos se solteiro / CPF se casado)                 | ALTA      |
| 190     | 190     | 1   | Num    | Flag desconhecida              | "1"                                                   | BAIXA     |
| 191     | 203     | 13  | Num    | Recibo / numero controle       | (variavel)                                            | BAIXA     |
| 204     | 213     | 10  | Alfa   | Reservado                      | (espacos)                                             | BAIXA     |
| 214     | 217     | 4   | Num    | Codigo endereco/municipio      | "1351"                                                | MEDIA     |
| 218     | 218     | 1   | Num    | Flag identificacao contrib     | "0"=nao marcado / "1"=marcado                         | MEDIA     |
| 219     | 226     | 8   | Num    | CEP                            | "25845060"                                            | ALTA      |
| 227     | 246     | 20  | Num    | Reservado                      | (zeros)                                               | BAIXA     |
| 247     | 253     | 7   | Num    | Imposto a pagar (centavos)     | "1480109" = R$ 14.801,09                              | ALTA      |
| 254     | 254     | 1   | Num    | Flag                           | "1"                                                   | BAIXA     |
| 255     | 265     | 11  | Num    | CPF repetido (Ajuste=CPF / Saida=espacos) | "41653508000" / "           "            | MEDIA     |
| 266     | 305     | 40  | Alfa   | Reservado                      | (espacos/zeros)                                       | BAIXA     |
| 306     | 330     | 25  | Num    | Zeros                          | (zeros)                                               | BAIXA     |
| 331     | 344     | 14  | Num    | CNPJ fonte principal           | "27865757000102"                                      | ALTA      |
| 345     | 384     | 40  | Alfa   | Reservado                      | (espacos)                                             | BAIXA     |
| 385     | 395     | 11  | Num    | CPF dependente/conjuge         | "13480293077"                                         | MEDIA     |
| 396     | 403     | 8   | Data   | Data nasc dependente           | "15032021"                                            | MEDIA     |
| 404     | 500     | 97  | Alfa   | Reservado                      | (espacos)                                             | BAIXA     |
| 501     | 511     | 11  | Num    | CPF medico/terceiro            | "66313835018"                                         | MEDIA     |
| 512     | 550     | 39  | Alfa   | Reservado                      | (espacos)                                             | BAIXA     |
| 551     | 590     | 40  | Alfa   | Cidade                         | "PETROPOLIS"                                          | ALTA      |
| 591     | 650     | 60  | Alfa   | Nome (repetido)                | "JORGE LUCAS DA SILVA MONTANO"                        | ALTA      |
| 651     | 660     | 10  | Num    | Hash / numero recibo           | "1618290403"                                          | BAIXA     |
| 661     | 661     | 1   | Alfa   | Separador                      | " " (espaco fixo)                                     | ALTA      |
| 662     | 673     | 12  | Alfnum | Codigo controle/hash           | "107C61A51159" (Ajuste) / "18C04DF542F3" (Saida)      | MEDIA     |
| 674     | 681     | 8   | Data   | **Data saida (AAAAMMDD)**      | "00000000" (Ajuste) / "20250531" (Saida)              | ALTA      |
| 682     | 682     | 1   | Num    | Flag procurador                | (variavel)                                            | MEDIA     |
| 683     | 693     | 11  | Num    | CPF procurador (saida)         | (espacos se sem procurador)                           | MEDIA     |
| 694     | 700     | 7   | Num    | Reservado                      | (zeros)                                               | BAIXA     |
| 701     | 713     | 13  | Num    | Valor (centavos)               | "36000000     "                                       | MEDIA     |
| 714     | 725     | 12  | Alfa   | Reservado                      | (espacos/zeros)                                       | BAIXA     |
| 726     | 778     | 53  | Num    | Valores financeiros            | (multiplos valores)                                   | MEDIA     |
| 779     | 792     | 14  | Alfa   | Reservado                      | (espacos/zeros)                                       | BAIXA     |
| 793     | 793     | 1   | Num    | **Cor/Raca (codigo)**          | "1"=Indigena / "2"=Branca(?)                          | ALTA      |
| 794     | 860     | 67  | Alfa   | Reservado                      | (espacos/zeros)                                       | BAIXA     |
| 861     | 880     | 20  | Alfnum | Flag + CPF medico repetido     | "10000000 66313835018"                                | MEDIA     |
| 881     | 1190    | 310 | Alfa   | Reservado / padding            | (espacos + zeros esparsos)                            | BAIXA     |
| 1191    | 1198    | 8   | Data   | **Data residencia no pais**    | (vazio se nao informado / "10112024"=10/11/2024)      | ALTA      |
| 1199    | 1234    | 36  | Alfa   | Reservado / padding            | (espacos/zeros)                                       | BAIXA     |
| 1235    | 1244    | 10  | Num    | **Checksum**                   | "3267140398"                                          | ALTA      |

---

## REGISTRO 16 - Dados Pessoais - 930 caracteres

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                               | Valor Encontrado                                | Confianca |
|---------|---------|-----|--------|-------------------------------------|-------------------------------------------------|-----------|
| 1       | 2       | 2   | Num    | Tipo registro                       | "16"                                            | ALTA      |
| 3       | 13      | 11  | Num    | CPF contribuinte                    | "41653508000"                                   | ALTA      |
| 14      | 73      | 60  | Alfa   | Nome completo                       | "JORGE LUCAS DA SILVA MONTANO"                  | ALTA      |
| 74      | 88      | 15  | Alfa   | Tipo logradouro                     | "RUA            "                               | ALTA      |
| 89      | 128     | 40  | Alfa   | Logradouro                          | "AV KOELER"                                     | ALTA      |
| 129     | 134     | 6   | Alfnum | Numero                              | "260   "                                        | ALTA      |
| 135     | 154     | 20  | Alfa   | Complemento                         | "CASA"                                          | ALTA      |
| 155     | 155     | 1   | Alfa   | **Separador obrigatorio**           | " " (SEMPRE espaco — nao sobrescrever)          | ALTA      |
| 156     | 174     | 19  | Alfa   | Bairro                              | "CENTRO"                                        | ALTA      |
| 175     | 182     | 8   | Num    | CEP                                 | "25845060"                                      | ALTA      |
| 183     | 183     | 1   | Alfa   | Separador                           | " "                                             | MEDIA     |
| 184     | 187     | 4   | Num    | Cod municipio (IBGE)                | "5877"                                          | ALTA      |
| 188     | 227     | 40  | Alfa   | Municipio                           | "PETROPOLIS"                                    | ALTA      |
| 228     | 229     | 2   | Alfa   | UF                                  | "RJ"                                            | ALTA      |
| 230     | 235     | 6   | Alfnum | Reservado/codigo                    | "   105"                                        | BAIXA     |
| 236     | 295     | 60  | Alfa   | Email                               | "JORGEMONTANO@GMAIL.COM"                        | ALTA      |
| 296     | 336     | 41  | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 337     | 347     | 11  | Num    | CPF do conjuge/companheiro          | (espacos se solteiro / CPF se casado)           | ALTA      |
| 348     | 349     | 2   | Num    | DDD telefone fixo                   | "24"                                            | MEDIA     |
| 350     | 360     | 11  | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 361     | 368     | 8   | Data   | Data nascimento (ddmmaaaa)          | "10102000"                                      | ALTA      |
| 369     | 381     | 13  | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 382     | 384     | 3   | Num    | Campo desconhecido                  | "000"                                           | BAIXA     |
| 385     | 386     | 2   | Num    | Codigo desconhecido                 | "01"                                            | BAIXA     |
| 387     | 387     | 1   | Alfa   | Desconhecido                        | "1"                                             | BAIXA     |
| 388     | 388     | 1   | Alfa   | Hipotese: endereco Brasil           | "S"                                             | BAIXA     |
| 389     | 389     | 1   | Alfa   | Hipotese: doenca grave/deficiencia  | "N"                                             | BAIXA     |
| 390     | 390     | 1   | Alfa   | Desconhecido                        | "S"                                             | BAIXA     |
| 391     | 391     | 1   | Alfa   | **Houve alteracao cadastral**       | "N"=Nao / "S"=Sim                               | ALTA      |
| 392     | 403     | 12  | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 404     | 410     | 7   | Num    | Zeros/reservado                     | "0000000"                                       | BAIXA     |
| 411     | 411     | 1   | Alfa   | Flag desconhecida                   | "N"                                             | BAIXA     |
| 412     | 420     | 9   | Alfnum | Campo desconhecido                  | "001062026"                                     | BAIXA     |
| 421     | 424     | 4   | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 425     | 425     | 1   | Num    | Flag desconhecida                   | "0"                                             | BAIXA     |
| 426     | 427     | 2   | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 428     | 428     | 1   | Alfa   | Flag desconhecida                   | "N"                                             | BAIXA     |
| 429     | 429     | 1   | Num    | Flag desconhecida                   | "0"                                             | BAIXA     |
| 430     | 440     | 11  | Num    | CNPJ empregador                     | (CNPJ da fonte / espacos se sem emprego)        | MEDIA     |
| 441     | 453     | 13  | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 454     | 454     | 1   | Alfa   | **Tipo declaracao (Reg16)**         | "A"=Ajuste Anual / "S"=Saida Definitiva         | ALTA      |
| 455     | 485     | 31  | Alfa   | Reservado                           | (espacos)                                       | BAIXA     |
| 486     | 487     | 2   | Num    | DDD celular                         | "24"                                            | ALTA      |
| 488     | 496     | 9   | Num    | Numero celular                      | "999999999"                                     | ALTA      |
| 497     | 497     | 1   | Alfa   | **Possui conjuge ou companheiro**   | "N"=Nao / "S"=Sim                               | ALTA      |
| 498     | 505     | 8   | Num    | Telefone fixo                       | "22429249"                                      | MEDIA     |
| 506     | 871     | 366 | Alfa   | Campos adicionais/reservado         | (espacos + dados esparsos)                      | BAIXA     |
| 872     | 872     | 1   | Num    | **Era residente no exterior**       | "0"=Nao / "1"=Sim                               | ALTA      |
| 873     | 880     | 8   | Data   | **Data de residencia no pais**      | (espacos se Nao / "10112024" se Sim, DDMMAAAA)  | ALTA      |
| 881     | 920     | 40  | Alfa   | Campos adicionais/reservado         | (espacos + dados esparsos)                      | BAIXA     |
| 921     | 930     | 10  | Num    | **Checksum**                        | "4068862196"                                    | ALTA      |

---

## REGISTRO 19 - Resumo Rendimentos por Fonte - 346 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                         | Valor Encontrado     | Confianca |
|---------|---------|-----|------|-------------------------------|----------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                 | "19"                 | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte              | "41653508000"        | ALTA      |
| 14      | 27      | 14  | Num  | CNPJ fonte pagadora           | "27865757000102"     | ALTA      |
| 28      | 336     | 309 | Num  | Campos valores (13 digitos)   | Multiplos blocos     | MEDIA     |
| 337     | 346     | 10  | Num  | **Checksum**                  | "4282350985"         | ALTA      |

**Blocos identificados no trecho 28-336** (grupos de 13 digitos):

| Bloco | Posicao | Valor BASE    | Valor R$       | Descricao                                   | Confianca |
|-------|---------|---------------|----------------|---------------------------------------------|-----------|
| 1     | 28-40   | 0000000000000 | R$ 0,00        | Imposto pago no exterior                    | ALTA      |
| 2     | 41-53   | 0000000000000 | R$ 0,00        | Imposto complementar                        | ALTA      |
| 3     | 54-66   | 0000000000000 | R$ 0,00        | Desconhecido                                | BAIXA     |
| 4     | 67-79   | 0000018000000 | R$ 180.000,00  | Total Exterior anual (soma Reg22 bloco 4)   | ALTA      |
| 5     | 80-92   | 0000000000000 | R$ 0,00        | Desconhecido                                | BAIXA     |
| 6     | 93-105  | 0000001800000 | R$ 18.000,00   | Total Darf pago anual (soma Reg22 pos 145-157) | ALTA   |
| 7-10  | 106-157 | 0000000000000 | R$ 0,00        | Campos zerados                              | BAIXA     |
| 11    | 158-170 | 0000001200000 | R$ 12.000,00   | Desconhecido                                | BAIXA     |
| 12    | 171-183 | 0000001200000 | R$ 12.000,00   | Contrib previdenciaria                      | ALTA      |
| 13    | 184-196 | 0000000000000 | R$ 0,00        | Desconhecido                                | BAIXA     |
| 14    | 197-209 | 0000001200000 | R$ 12.000,00   | Contrib previdenciaria (confirmacao)        | ALTA      |

**Observacao**: Reg 19 e derivado dos Reg 22. Bloco 4 = soma dos 12 meses de Reg 22 (Exterior). Bloco 6 = soma dos 12 meses de Reg 22 (Darf).

---

## REGISTRO 20 - Resumo de Calculos / Totais - 926 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                    | Valor Encontrado  | Confianca |
|---------|---------|-----|------|--------------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro            | "20"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte         | "41653508000"     | ALTA      |
| 14      | 916     | 903 | Num  | Blocos de valores (13d)  | Multiplos valores | MEDIA     |
| 917     | 926     | 10  | Num  | **Checksum**             | "2345898296"      | ALTA      |

**Blocos confirmados** (grupos de 13 digitos a partir de pos 14):

| Bloco | Posicao  | Valor BASE    | Valor R$ BASE   | Descricao                              | Confianca |
|-------|----------|---------------|-----------------|----------------------------------------|-----------|
| 1     | 14-26    | 0000018000000 | R$ 180.000,00   | Rendimentos tributaveis PJ             | ALTA      |
| 2     | 27-39    | 0000018000000 | R$ 180.000,00   | Rendimentos (copia)                    | MEDIA     |
| 5     | 66-78    | 0000036000000 | R$ 360.000,00   | Total rendimentos brutos               | ALTA      |
| 6     | 79-91    | 0000001200000 | R$ 12.000,00    | Deducoes previdencia                   | ALTA      |
| 14    | 183-195  | 0000001816408 | R$ 18.164,08    | Parcela a deduzir tabela               | MEDIA     |
| 15    | 196-208  | 0000034183592 | R$ 341.835,92   | Base calculo (rend bruto - deducoes)   | MEDIA     |
| 16    | 209-221  | 0000008315109 | R$ 83.151,09    | Imposto calculado tabela               | ALTA      |
| 18    | 235-247  | 0000008315109 | R$ 83.151,09    | Imposto calculado (copia 1)            | ALTA      |
| 20    | 261-273  | 0000008315109 | R$ 83.151,09    | Imposto calculado (copia 2)            | ALTA      |
| 21    | 274-286  | 0000008315109 | R$ 83.151,09    | Imposto calculado (copia 3)            | ALTA      |
| 22    | 287-299  | 0000005000000 | R$ 50.000,00    | Imposto retido na fonte                | ALTA      |
| 24    | 313-325  | 0000000000000 | R$ 0,00         | Imposto complementar                   | ALTA      |
| 25    | 326-338  | 0000000000000 | R$ 0,00         | Imposto pago exterior                  | ALTA      |
| 27    | 352-364  | 0000006835000 | R$ 68.350,00    | Total impostos pagos (acumulado)       | MEDIA     |
| 29    | 378-390  | 0000001480109 | R$ 14.801,09    | **Imposto a pagar (deducoes leg)**     | ALTA      |
| 30    | 391-403  | 1000000148010 | cod+valor       | Cod tributacao + imposto a pagar       | MEDIA     |
| 36    | 469-481  | 0000000050000 | R$ 500,00       | Calculo derivado rendimento isento     | MEDIA     |
| 37    | 482-494  | 0000000230000 | R$ 2.300,00     | Desconhecido                           | MEDIA     |
| 45    | 586-598  | 0000000050000 | R$ 500,00       | Calculo derivado rend isento (copia 36)| MEDIA     |
| 47    | 612-624  | 0000000230000 | R$ 2.300,00     | Desconhecido (copia 37)                | MEDIA     |
| 68    | 885-897  | 0023090000000 | 23,09%          | **Aliquota efetiva (deducoes leg)**    | ALTA      |

---

## REGISTRO 21 - Rendimentos Tributaveis Recebidos de PJ - 170 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor BASE                            | Valor R$ BASE     | Confianca |
|---------|---------|-----|------|------------------------------|---------------------------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "21"                                  |                   | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                         |                   | ALTA      |
| 14      | 27      | 14  | Num  | CNPJ fonte pagadora          | "27865757000102"                      |                   | ALTA      |
| 28      | 87      | 60  | Alfa | Nome fonte pagadora          | "GLOBO COMUNICACAO E PARTICIPACOES S/A" |               | ALTA      |
| 88      | 100     | 13  | Num  | Rendimentos recebidos (c)    | "0000018000000"                       | R$ 180.000,00     | ALTA      |
| 101     | 113     | 13  | Num  | Contrib previdenciaria (c)   | "0000001200000"                       | R$ 12.000,00      | ALTA      |
| 114     | 126     | 13  | Num  | 13o salario (c)              | "0000001200000"                       | R$ 12.000,00      | ALTA      |
| 127     | 139     | 13  | Num  | Imposto retido fonte (c)     | "0000005000000"                       | R$ 50.000,00      | ALTA      |
| 140     | 147     | 8   | Alfa | Reservado                    | (espacos)                             |                   | BAIXA     |
| 148     | 160     | 13  | Num  | IRRF sobre 13o salario (c)   | "0000000100000"                       | R$ 1.000,00       | ALTA      |
| 161     | 170     | 10  | Num  | **Checksum**                 | "0479665743"                          |                   | ALTA      |

**Atencao**: A ordem no arquivo e: Rendimentos > Contrib Prev > 13o Salario > IR Retido Fonte (difere da tela do programa).

---

## REGISTRO 22 - Rendimentos Mensais PF/Exterior - 167 caracteres (12 registros, jan-dez)

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

**Valores BASE**: R$ 15.000/mes no bloco Exterior. Darf BASE = R$ 1.500/mes.
12 x R$ 15.000 = R$ 180.000 (soma anual Exterior = Reg 19 bloco 4).
12 x R$ 1.500 = R$ 18.000 (soma anual Darf = Reg 19 bloco 6).

---

## REGISTRO 23 - Imposto Pago / Codigo - 40 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                | Valor Encontrado               | Confianca |
|---------|---------|-----|------|----------------------|--------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro        | "23"                           | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte     | "41653508000"                  | ALTA      |
| 14      | 17      | 4   | Num  | Codigo/sequencial    | "0001"                         | MEDIA     |
| 18      | 30      | 13  | Num  | Valor (centavos)     | "0000000500000" = R$ 5.000,00  | ALTA      |
| 31      | 40      | 10  | Num  | **Checksum**         | "2292057427"                   | ALTA      |

---

## REGISTRO 24 - Deducoes Legais - 40 caracteres (3 registros)

| Pos Ini | Pos Fim | Tam | Tipo | Campo                | Valor Encontrado  | Confianca |
|---------|---------|-----|------|----------------------|-------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro        | "24"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte     | "41653508000"     | ALTA      |
| 14      | 17      | 4   | Num  | Codigo deducao       | "0001/0006/0007"  | MEDIA     |
| 18      | 30      | 13  | Num  | Valor (centavos)     | Ver abaixo        | MEDIA     |
| 31      | 40      | 10  | Num  | **Checksum**         | (variavel)        | ALTA      |

**Codigos encontrados**:
- `0001`: R$ 12.000,00 — Previdencia oficial
- `0006`: R$ 10.000,00 — Tributacao exclusiva/definitiva
- `0007`: R$ 1.000,00 — Rendimentos recebidos acumuladamente (RRA)

---

## REGISTRO 25 - Dependentes - 224 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                      | Valor Encontrado                            | Confianca |
|---------|---------|-----|------|----------------------------|---------------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro              | "25"                                        | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte           | "41653508000"                               | ALTA      |
| 14      | 18      | 5   | Num  | Sequencial dependente      | "00001"                                     | ALTA      |
| 19      | 20      | 2   | Num  | Tipo dependente            | "21" (filho/enteado ate 21 anos)            | ALTA      |
| 21      | 80      | 60  | Alfa | Nome dependente            | "RYAN SILVA MONTANO"                        | ALTA      |
| 81      | 88      | 8   | Data | Data nascimento (DDMMAAAA) | "15032021"                                  | ALTA      |
| 89      | 99      | 11  | Num  | CPF dependente             | "13480293077"                               | ALTA      |
| 100     | 111     | 12  | ???  | Desconhecido               | (espacos)                                   | BAIXA     |
| 112     | 112     | 1   | Num  | Mora com o titular         | "0"=nao / "1"=sim                           | ALTA      |
| 113     | 202     | 90  | Alfa | Email                      | "TESTEDEPENDENTE@GMAIL.COM" (padded)        | ALTA      |
| 203     | 204     | 2   | Num  | DDD celular                | "22"                                        | ALTA      |
| 205     | 213     | 9   | Num  | Celular (sem hifen)        | "989898989"                                 | ALTA      |
| 214     | 214     | 1   | Num  | Tipo telefone (constante)  | "2"                                         | BAIXA     |
| 215     | 224     | 10  | Num  | **Checksum**               | "0063170408"                                | ALTA      |

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
| 146     | 146     | 1   | Alfa | Tipo (T=titular)             | "T"                             | MEDIA     |
| 147     | 661     | 515 | Alfa | Descricao / historico        | "PAGAMENTO DE MEDICO"           | ALTA      |
| 662     | 671     | 10  | Num  | **Checksum**                 | "1554688970"                    | ALTA      |

---

## REGISTRO 27 - Bens e Direitos - 1251 caracteres

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                       | Valor Encontrado                              | Confianca |
|---------|---------|-----|--------|-----------------------------|-----------------------------------------------|-----------|
| 1       | 2       | 2   | Num    | Tipo registro               | "27"                                          | ALTA      |
| 3       | 13      | 11  | Num    | CPF contribuinte            | "41653508000"                                 | ALTA      |
| 14      | 15      | 2   | Num    | Codigo item                 | "11" (Apartamento)                            | ALTA      |
| 16      | 16      | 1   | Num    | Flag exterior               | "0"=Nacional / "1"=Exterior                   | ALTA      |
| 17      | 19      | 3   | Num    | Codigo pais (BACEN)         | "105"=Brasil / "149"=Canada / "756"=Africa Sul | ALTA      |
| 20      | 519     | 500 | Alfa   | Descricao / discriminacao   | "APARTAMENTO PETROPOLIS"                      | ALTA      |
| 520     | 531     | 12  | Alfa   | Reservado                   | (espacos)                                     | BAIXA     |
| 532     | 544     | 13  | Num    | Valor em 31/12 anterior (c) | "0000010000000" = R$ 100.000,00               | ALTA      |
| 545     | 557     | 13  | Num    | Valor em 31/12 atual (c)    | "0000020000000" = R$ 200.000,00               | ALTA      |
| 558     | 597     | 40  | Alfa   | Logradouro do bem           | "AV KOELER"                                   | ALTA      |
| 598     | 603     | 6   | Alfnum | Numero                      | "260   "                                      | ALTA      |
| 604     | 643     | 40  | Alfa   | Complemento                 | "PREDIO"                                      | ALTA      |
| 644     | 683     | 40  | Alfa   | Bairro                      | "CENTRO"                                      | ALTA      |
| 684     | 691     | 8   | Num    | CEP do bem                  | "25840600"                                    | ALTA      |
| 692     | 692     | 1   | Alfa   | Separador                   | " "                                           | BAIXA     |
| 693     | 694     | 2   | Alfa   | UF                          | "RJ"                                          | ALTA      |
| 695     | 698     | 4   | Num    | Cod municipio IBGE          | "5877"                                        | ALTA      |
| 699     | 738     | 40  | Alfa   | Municipio                   | "PETROPOLIS"                                  | ALTA      |
| 739     | 862     | 124 | Alfa   | Campos adicionais           | (diversos)                                    | BAIXA     |
| 863     | 866     | 4   | Num    | Agencia                     | "8452"                                        | ALTA      |
| 867     | 879     | 13  | Alfa   | Reservado                   | (espacos)                                     | BAIXA     |
| 880     | 880     | 1   | Num    | Digito verificador          | "8"                                           | ALTA      |
| 881     | 891     | 11  | Alfa   | Reservado                   | (espacos)                                     | BAIXA     |
| 892     | 895     | 4   | Num    | Reservado                   | "0000"                                        | BAIXA     |
| 896     | 896     | 1   | Num    | Desconhecido                | "1" (com data) / "5" (sem data)               | BAIXA     |
| 897     | 904     | 8   | Num    | **Data de aquisicao (DDMMAAAA)** | "05062025"                               | ALTA      |
| 905     | 932     | 28  | Alfa   | Reservado                   | (espacos)                                     | BAIXA     |
| 933     | 943     | 11  | Num    | RENAVAM (somente veiculos)  | (vazio em imoveis)                            | ALTA      |
| 944     | 956     | 13  | Alfnum | Numero da conta             | "2222333333444"                               | ALTA      |
| 957     | 1025    | 69  | Alfa   | Campos adicionais           | (espacos + dados esparsos)                    | BAIXA     |
| 1026    | 1038    | 13  | Num    | Aplic Fin. Renda ou Perda   | "0000008888800"                               | ALTA      |
| 1039    | 1051    | 13  | Num    | Aplic Fin. Imposto pago Ext | "0000009999900"                               | ALTA      |
| 1052    | 1100    | 49  | Alfa   | Campos adicionais / padding | (zeros e espacos)                             | BAIXA     |
| 1101    | 1102    | 2   | Num    | **Codigo grupo**            | "01"=Imoveis / "02"=Moveis                    | ALTA      |
| 1103    | 1103    | 1   | Num    | Desconhecido                | "0"                                           | BAIXA     |
| 1104    | 1241    | 138 | Alfa   | Campos adicionais / padding | (espacos e zeros)                             | BAIXA     |
| 1242    | 1251    | 10  | Num    | **Checksum**                | "0820608248"                                  | ALTA      |

---

## REGISTRO 28 - Dividas e Onus Reais - 576 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor Encontrado                | Confianca |
|---------|---------|-----|------|------------------------------|---------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                | "28"                            | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"                   | ALTA      |
| 14      | 15      | 2   | Num  | Codigo divida                | "11"                            | ALTA      |
| 16      | 515     | 500 | Alfa | Descricao / discriminacao    | "EMPRESTIMO PARA APARTAMENTO"   | ALTA      |
| 516     | 527     | 12  | Alfa | Reservado                    | (espacos)                       | BAIXA     |
| 528     | 540     | 13  | Num  | Saldo em 31/12 anterior (c)  | "0000015000000" = R$ 150.000,00 | MEDIA     |
| 541     | 553     | 13  | Num  | Saldo em 31/12 atual (c)     | "0000020000000" = R$ 200.000,00 | MEDIA     |
| 554     | 566     | 13  | Num  | Valor pago no ano (c)        | "0000002000000" = R$ 20.000,00  | MEDIA     |
| 567     | 576     | 10  | Num  | **Checksum**                 | "2925550781"                    | ALTA      |

---

## REGISTRO 39 - Declaracao de Saida Definitiva - 193 caracteres

Presente apenas em declaracoes do tipo Saida Definitiva (IRPF-S).

| Pos Ini | Pos Fim | Tam | Tipo | Campo                                 | Valor Encontrado              | Confianca |
|---------|---------|-----|------|---------------------------------------|-------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                         | "39"                          | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte                      | "13103517750"                 | ALTA      |
| 14      | 24      | 11  | Num  | **CPF do procurador**                 | "98736521263"                 | ALTA      |
| 25      | 84      | 60  | Alfa | **Nome do procurador**                | "NOME DO PROCURADOR"          | ALTA      |
| 85      | 164     | 80  | Alfa | **Endereco do procurador**            | "ENDERECO DO PROCURADO PARA TESTE" | ALTA |
| 165     | 172     | 8   | Data | **Data nao residente (DDMMAAAA)**     | "10102025"                    | ALTA      |
| 173     | 180     | 8   | Data | **Data residente no pais (DDMMAAAA)** | "09092025"                    | ALTA      |
| 181     | 183     | 3   | Num  | **Codigo pais destino (BACEN)**       | "149" (Canada)                | ALTA      |
| 184     | 193     | 10  | Num  | **Checksum**                          | "0617626333"                  | ALTA      |

**Atencao**: Formato de data aqui e DDMMAAAA, diferente do header IRPF (AAAAMMDD para data de saida).

---

## REGISTRO 45 - Rendimentos Recebidos Acumuladamente (RRA) - 216 caracteres

Um registro por fonte pagadora. Aparece quando ha RRA informado no programa IRPF.

| Pos Ini | Pos Fim | Tam | Tipo   | Campo                              | Valor de Referencia               | Confianca |
|---------|---------|-----|--------|------------------------------------|-----------------------------------|-----------|
| 1       | 2       | 2   | Num    | Tipo registro                      | "45"                              | ALTA      |
| 3       | 13      | 11  | Num    | CPF contribuinte                   | "71926456130"                     | ALTA      |
| 14      | 15      | 2   | Alfa   | Reservado                          | "  " (espacos)                    | BAIXA     |
| 16      | 29      | 14  | Num    | CNPJ fonte pagadora                | "27865757000102"                  | ALTA      |
| 30      | 89      | 60  | Alfa   | Nome fonte pagadora                | "GLOBO COMUNICACAO..."            | ALTA      |
| 90      | 102     | 13  | Num    | Zeros                              | "0000000000000"                   | ALTA      |
| 103     | 115     | 13  | Num    | **Contrib previdenciaria (c)**     | "0000000033365" = R$ 333,65       | ALTA      |
| 116     | 128     | 13  | Num    | Parcela isenta 65 anos (c)         | "0000000000000"                   | BAIXA     |
| 129     | 141     | 13  | Num    | **Imposto retido na fonte (c)**    | "0000000002164" = R$ 21,64        | ALTA      |
| 142     | 143     | 2   | Num    | **Mes do recebimento**             | "01"=Jan / "03"=Mar / etc.        | ALTA      |
| 144     | 148     | 5   | Alfnum | Metadados/flags RRA                | "00001"                           | BAIXA     |
| 149     | 149     | 1   | Alfa   | Separador                          | " " (espaco fixo)                 | BAIXA     |
| 150     | 152     | 3   | Num    | Flag/codigo interno                | "100"                             | BAIXA     |
| 153     | 153     | 1   | Num    | **Numero de meses RRA**            | "3"                               | ALTA      |
| 154     | 154     | 1   | Num    | Reservado                          | "0"                               | BAIXA     |
| 155     | 167     | 13  | Num    | **Imposto bruto RRA (c)**          | "0000000000000" (zero se abaixo da faixa tributavel) | ALTA |
| 168     | 180     | 13  | Num    | **Rendimentos tributaveis RRA (c)**| "0000000085465" = R$ 854,65       | ALTA      |
| 181     | 193     | 13  | Num    | **Rendimentos RRA (copia 168-180)**| "0000000085465" = R$ 854,65       | ALTA      |
| 194     | 206     | 13  | Num    | Campo desconhecido                 | "0000000003652" = R$ 36,52        | BAIXA     |
| 207     | 216     | 10  | Num    | **Checksum**                       | "3948607698"                      | ALTA      |

**Calculo do imposto bruto RRA** (pos 155-167):
- Imposto bruto = 27,5% x rendimentos (pos 168-180)
- Imposto exibido no formulario = imposto bruto - 27,5% x contrib prev (pos 103-115)

---

## REGISTRO 80 - Rendimento com Exigibilidade Suspensa - 123 caracteres

Um registro por fonte pagadora. Armazena rendimentos cujo imposto esta com exigibilidade suspensa por decisao judicial/administrativa.

| Pos Ini | Pos Fim | Tam | Tipo | Campo                              | Valor Encontrado                      | Confianca |
|---------|---------|-----|------|------------------------------------|---------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                      | "80"                                  | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte                   | "71926456130"                         | ALTA      |
| 14      | 27      | 14  | Num  | CNPJ fonte pagadora                | "27865757000102"                      | ALTA      |
| 28      | 87      | 60  | Alfa | Nome fonte pagadora                | "GLOBO COMUNICACAO E PARTICIPACOES S/A 123" | ALTA |
| 88      | 100     | 13  | Num  | **Rendimentos tributaveis (c)**    | "0000000652412" = R$ 6.524,12         | ALTA      |
| 101     | 113     | 13  | Num  | **Depositos judiciais (c)**        | "0000000001152" = R$ 11,52            | ALTA      |
| 114     | 123     | 10  | Num  | **Checksum**                       | "2598594378"                          | ALTA      |

**Efeitos no Reg 20** (confirmados):
- Pos 105-117: R$ 4.550,16 (valor derivado — formula a confirmar)
- Pos 183-195: R$ 4.550,16 (copia)
- Pos 652-664: copia de rendimentos tributaveis
- Pos 678-690: copia de depositos judiciais

---

## REGISTRO 84 - Lancamento Individual de Rendimento Isento/Nao Tributavel - 144 caracteres

Um registro por lancamento informado pelo usuario. Pode ser para Titular (T) ou Dependente (D).

| Pos Ini | Pos Fim | Tam | Tipo | Campo                           | Valor BASE                            | Confianca |
|---------|---------|-----|------|---------------------------------|---------------------------------------|-----------|
| 1       | 2       | 2   | Num  | Tipo registro                   | "84"                                  | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte                | "41653508000"                         | ALTA      |
| 14      | 14      | 1   | Alfa | Tipo beneficiario (T=titular)   | "T"                                   | ALTA      |
| 15      | 25      | 11  | Num  | CPF beneficiario                | "41653508000"                         | ALTA      |
| 26      | 29      | 4   | Num  | Codigo tipo rendimento          | "0001" (01=Bolsas de estudo)          | ALTA      |
| 30      | 43      | 14  | Num  | CNPJ fonte pagadora             | "27865757000102"                      | ALTA      |
| 44      | 103     | 60  | Alfa | Nome fonte pagadora             | "GLOBO COMUNICACAO E PARTICIPACOES S/A" | ALTA    |
| 104     | 116     | 13  | Num  | **Valor rendimento isento (c)** | "0000000500000" = R$ 5.000,00         | ALTA      |
| 117     | 121     | 5   | Num  | Reservado                       | "00000"                               | BAIXA     |
| 122     | 134     | 13  | Num  | Valor adicional                 | "0000000000000"                       | BAIXA     |
| 135     | 144     | 10  | Num  | **Checksum**                    | "3479762237"                          | ALTA      |

---

## REGISTRO 88 - Lancamento Individual de Rendimento Sujeito a Tributacao Exclusiva/Definitiva - 131 caracteres

Um registro por lancamento. Estrutura identica ao Registro 84.

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
| 117     | 121     | 5   | Num  | Reservado                          | "00000"                               | BAIXA     |
| 122     | 131     | 10  | Num  | **Checksum**                       | "2698011732"                          | ALTA      |

---

## REGISTRO T9 - Trailer / Totalizador - 449 caracteres

| Pos Ini | Pos Fim | Tam | Tipo | Campo                        | Valor Encontrado  | Confianca |
|---------|---------|-----|------|------------------------------|-------------------|-----------|
| 1       | 2       | 2   | Alfa | Tipo registro                | "T9"              | ALTA      |
| 3       | 13      | 11  | Num  | CPF contribuinte             | "41653508000"     | ALTA      |
| 14      | 21      | 8   | Num  | Total de registros           | "00000028"        | ALTA      |
| 22      | 439     | 418 | Num  | Contadores por tipo registro | Ver abaixo        | MEDIA     |
| 440     | 449     | 10  | Num  | **Checksum**                 | "4240261545"      | ALTA      |

**Contadores identificados** (blocos de 5 digitos = qtd registros por tipo):

| Posicao | Valor   | Interpretacao                 |
|---------|---------|-------------------------------|
| 22-26   | "00001" | 1 registro IRPF (header)      |
| 37-41   | "00001" | 1 registro tipo 16            |
| 42-46   | "00001" | 1 registro tipo 19            |
| 52-56   | "00012" | 12 registros tipo 22          |
| 57-61   | "00001" | 1 registro tipo 23            |
| 62-66   | "00003" | 3 registros tipo 24           |
| 67-71   | "00001" | 1 registro tipo 25            |
| 72-76   | "00001" | 1 registro tipo 26            |
| 77-81   | "00001" | 1 registro tipo 27            |
| 82-86   | "00001" | 1 registro tipo 28            |

---

## Observacoes Gerais

### Estrutura padrao de todos os registros:
```
[Tipo 2-4 chars][CPF 11 chars][...campos...][Checksum 10 chars]
```

### Convencoes monetarias:
- 13 digitos numericos com zeros a esquerda
- Valores em centavos (ultimos 2 digitos implicitos = casas decimais)
- Exemplo: `0000018000000` = 18.000.000 centavos = R$ 180.000,00
- Exemplo: `0000001200000` = 1.200.000 centavos = R$ 12.000,00

### Campos de data:
- Header IRPF (pos 674-681): AAAAMMDD
- Reg 39 (pos 165-172, 173-180): DDMMAAAA
- Reg 16 (pos 361-368, 873-880): DDMMAAAA
- Reg 25 (pos 81-88): DDMMAAAA
- Reg 27 (pos 897-904): DDMMAAAA

### Declaracao de Saida Definitiva — campos obrigatorios:
Alem de criar o Reg 39, alterar obrigatoriamente:
- Header IRPF pos 19-20: "00" → "20"
- Header IRPF pos 21: "0" → " " (espaco) — alterar junto com pos 19-20
- Header IRPF pos 674-681: preencher data saida (AAAAMMDD)
- Header IRPF pos 255-265: espacos (nao repetir CPF)
- Reg 16 pos 454: "A" → "S"
