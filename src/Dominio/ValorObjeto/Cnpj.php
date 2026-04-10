<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

final readonly class Cnpj
{
    public string $valor;

    public function __construct(string $valor)
    {
        $valor = preg_replace('/\D/', '', $valor);

        if (strlen($valor) !== 14) {
            throw new \InvalidArgumentException(
                "CNPJ deve ter 14 digitos, recebido: '{$valor}' (" . strlen($valor) . " digitos)"
            );
        }

        $this->valor = $valor;
    }

    public function __toString(): string
    {
        return $this->valor;
    }

    public function igual(self $outro): bool
    {
        return $this->valor === $outro->valor;
    }
}
