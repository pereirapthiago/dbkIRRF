<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Validador;

final readonly class ResultadoValidacao
{
    /**
     * @param list<string> $erros
     */
    public function __construct(
        public bool $valido,
        public array $erros = [],
    ) {
    }
}
