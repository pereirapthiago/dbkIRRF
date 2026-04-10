<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Enum\TipoCampo;

/**
 * Define o layout posicional de um registro DBK.
 * Colecao tipada de CampoDTO indexada por nome.
 * Fonte unica de verdade para posicoes de campos.
 */
final class LayoutRegistro
{
    /** @var array<string, CampoDTO> */
    private array $campos = [];

    public function __construct(CampoDTO ...$campos)
    {
        foreach ($campos as $campo) {
            $this->campos[$campo->nome] = $campo;
        }
    }

    public function campo(string $nome): CampoDTO
    {
        if (!isset($this->campos[$nome])) {
            throw new \InvalidArgumentException("Campo '{$nome}' nao encontrado no layout");
        }

        return $this->campos[$nome];
    }

    /** @return list<CampoDTO> */
    public function obterTodos(): array
    {
        return array_values($this->campos);
    }

    /**
     * Extrai valor bruto do campo (preserva espacos/zeros).
     */
    public function extrair(string $linha, string $nome): string
    {
        $campo = $this->campo($nome);

        return substr($linha, $campo->posicaoInicial - 1, $campo->tamanho);
    }

    /**
     * Extrai valor de texto (remove espacos a direita).
     */
    public function extrairTexto(string $linha, string $nome): string
    {
        return rtrim($this->extrair($linha, $nome));
    }

    /**
     * Extrai valor numerico como inteiro.
     */
    public function extrairNumero(string $linha, string $nome): int
    {
        $valor = ltrim($this->extrair($linha, $nome), '0') ?: '0';

        return (int) $valor;
    }

    /**
     * Monta linha posicional a partir de valores pre-formatados.
     * Exclui campo 'checksum' (adicionado por GeradorRegistroBase).
     * Campos nao fornecidos sao preenchidos com padding padrao
     * (espacos para ALFA/ALFANUMERICO/DATA, zeros para NUMERICO).
     *
     * @param array<string, string> $valores Campo => valor ja formatado
     */
    public function montarLinha(array $valores): string
    {
        $tamanhoTotal = 0;

        foreach ($this->campos as $nome => $campo) {
            if ($nome === 'checksum') {
                continue;
            }
            $fim = $campo->posicaoInicial + $campo->tamanho - 1;
            if ($fim > $tamanhoTotal) {
                $tamanhoTotal = $fim;
            }
        }

        $linha = str_repeat(' ', $tamanhoTotal);

        foreach ($this->campos as $nome => $campo) {
            if ($nome === 'checksum' || isset($valores[$nome])) {
                continue;
            }
            $padding = $campo->tipo === TipoCampo::NUMERICO
                ? str_repeat('0', $campo->tamanho)
                : str_repeat(' ', $campo->tamanho);
            $linha = substr_replace($linha, $padding, $campo->posicaoInicial - 1, $campo->tamanho);
        }

        foreach ($valores as $nome => $valor) {
            $campo = $this->campo($nome);
            $tamanhoValor = strlen($valor);
            if ($tamanhoValor !== $campo->tamanho) {
                throw new \RuntimeException(
                    "Campo '{$nome}': valor tem {$tamanhoValor} chars, esperado {$campo->tamanho}"
                );
            }
            $linha = substr_replace($linha, $valor, $campo->posicaoInicial - 1, $campo->tamanho);
        }

        return $linha;
    }
}
