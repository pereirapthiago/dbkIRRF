<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

/**
 * Codigo do grupo de bem/direito (2 digitos).
 * Aceita qualquer codigo valido para nao quebrar se a Receita adicionar novos.
 * Constantes fornecidas para os grupos conhecidos.
 */
final readonly class CodigoGrupoBem
{
    public const BENS_IMOVEIS = '01';
    public const BENS_MOVEIS = '02';
    public const PARTICIPACOES_SOCIETARIAS = '03';
    public const APLICACOES_INVESTIMENTOS = '04';
    public const CREDITOS = '05';
    public const DEPOSITOS_VISTA_NUMERARIO = '06';
    public const FUNDOS = '07';
    public const CRIPTOATIVOS = '08';
    public const PREVIDENCIA_COMPLEMENTAR = '09';
    public const CONSORCIO = '10';
    public const OUTROS = '99';

    private const DESCRICOES = [
        '01' => 'Bens imoveis',
        '02' => 'Bens moveis',
        '03' => 'Participacoes societarias',
        '04' => 'Aplicacoes e investimentos',
        '05' => 'Creditos',
        '06' => 'Depositos a vista e numerario',
        '07' => 'Fundos',
        '08' => 'Criptoativos',
        '09' => 'Previdencia complementar',
        '10' => 'Consorcio',
        '99' => 'Outros bens e direitos',
    ];

    public string $valor;

    public function __construct(string $valor)
    {
        $valor = str_pad($valor, 2, '0', STR_PAD_LEFT);

        if (strlen($valor) !== 2) {
            throw new \InvalidArgumentException(
                "Codigo grupo bem deve ter 2 digitos: '{$valor}'"
            );
        }

        $this->valor = $valor;
    }

    public function obterDescricao(): string
    {
        return self::DESCRICOES[$this->valor] ?? "Grupo de bem {$this->valor}";
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
