<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

final readonly class Data
{
    public string $valor;

    public function __construct(string $valor)
    {
        if (strlen($valor) !== 8) {
            throw new \InvalidArgumentException(
                "Data deve ter 8 caracteres (ddmmaaaa), recebido: '{$valor}'"
            );
        }

        if ($valor !== '00000000' && $valor !== '        ' && !ctype_digit($valor)) {
            throw new \InvalidArgumentException(
                "Data deve conter apenas digitos ou ser vazia: '{$valor}'"
            );
        }

        $this->valor = $valor;
    }

    public static function deDateTime(\DateTimeInterface $data): self
    {
        return new self($data->format('dmY'));
    }

    public static function vazia(): self
    {
        return new self('00000000');
    }

    public static function espacosVazios(): self
    {
        return new self('        ');
    }

    public function eVazia(): bool
    {
        return $this->valor === '00000000' || trim($this->valor) === '';
    }

    public function obterDia(): int
    {
        return (int) substr($this->valor, 0, 2);
    }

    public function obterMes(): int
    {
        return (int) substr($this->valor, 2, 2);
    }

    public function obterAno(): int
    {
        return (int) substr($this->valor, 4, 4);
    }

    public function __toString(): string
    {
        return $this->valor;
    }
}
