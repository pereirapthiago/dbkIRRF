<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

final readonly class Checksum
{
    public string $valor;

    public function __construct(string $valor)
    {
        if (strlen($valor) !== 10) {
            throw new \InvalidArgumentException(
                "Checksum deve ter 10 caracteres, recebido: '{$valor}' (" . strlen($valor) . " chars)"
            );
        }

        $this->valor = $valor;
    }

    /**
     * Placeholder enquanto o algoritmo de checksum nao e conhecido.
     */
    public static function placeholder(): self
    {
        return new self('0000000000');
    }

    public static function deLinha(string $linha): self
    {
        return new self(substr($linha, -10));
    }

    public function __toString(): string
    {
        return $this->valor;
    }
}
