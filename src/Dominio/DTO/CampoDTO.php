<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Enum\TipoCampo;

/**
 * Define a estrutura de um campo posicional dentro de uma linha DBK.
 * Usado para descrever o layout de cada registro de forma declarativa.
 */
final readonly class CampoDTO
{
    public function __construct(
        public string $nome,
        public int $posicaoInicial,
        public int $tamanho,
        public TipoCampo $tipo,
        public bool $obrigatorio = true,
        public string $descricao = '',
        public ?string $valorPadrao = null,
    ) {
    }

    public function obterPosicaoFinal(): int
    {
        return $this->posicaoInicial + $this->tamanho - 1;
    }

    /**
     * Extrai o valor deste campo de uma linha DBK.
     * Posicoes sao 1-based conforme documentacao.
     */
    public function extrairDeLinha(string $linha): string
    {
        return substr($linha, $this->posicaoInicial - 1, $this->tamanho);
    }
}
