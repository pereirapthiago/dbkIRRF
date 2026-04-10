<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

final readonly class ValorMonetario
{
    public function __construct(
        public int $centavos,
    ) {
        if ($centavos < 0) {
            throw new \InvalidArgumentException(
                "Valor monetario nao pode ser negativo: {$centavos}"
            );
        }
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function deCentavos(int $centavos): self
    {
        return new self($centavos);
    }

    public static function deReais(float $reais): self
    {
        return new self((int) round($reais * 100));
    }

    public static function deString(string $valor): self
    {
        $valor = ltrim($valor, '0') ?: '0';

        return new self((int) $valor);
    }

    public function emReais(): float
    {
        return $this->centavos / 100;
    }

    public function formatar(int $tamanho = 13): string
    {
        return str_pad((string) $this->centavos, $tamanho, '0', STR_PAD_LEFT);
    }

    public function __toString(): string
    {
        return $this->formatar();
    }

    public function igual(self $outro): bool
    {
        return $this->centavos === $outro->centavos;
    }
}
