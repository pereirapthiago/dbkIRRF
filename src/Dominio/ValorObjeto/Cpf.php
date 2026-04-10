<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

final readonly class Cpf
{
    public string $valor;

    public function __construct(string $valor)
    {
        $valor = preg_replace('/\D/', '', $valor);

        if (strlen($valor) !== 11) {
            throw new \InvalidArgumentException(
                "CPF deve ter 11 digitos, recebido: '{$valor}' (" . strlen($valor) . " digitos)"
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
