<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\ValorObjeto;

/**
 * Codigo do tipo de dependente (2 digitos).
 * Aceita qualquer codigo valido para nao quebrar se a Receita adicionar novos.
 * Constantes fornecidas para os tipos conhecidos.
 */
final readonly class CodigoDependente
{
    public const COMPANHEIRO = '11';
    public const FILHO_ATE_21 = '21';
    public const FILHO_UNIVERSITARIO_ATE_24 = '22';
    public const IRMAO_NETO_SEM_ARRIMO_ATE_21 = '23';
    public const IRMAO_NETO_UNIVERSITARIO_ATE_24 = '24';
    public const PAIS_AVOS_BISAVOS = '25';
    public const MENOR_POBRE_ATE_21 = '26';
    public const INCAPAZ_TUTELADO = '31';
    public const AGREGADO_OUTRO = '41';
    public const PESSOA_ABSOLUTAMENTE_INCAPAZ = '51';

    private const DESCRICOES = [
        '11' => 'Companheiro(a)',
        '21' => 'Filho(a) ou enteado(a) ate 21 anos',
        '22' => 'Filho(a) ou enteado(a) universitario ate 24 anos',
        '23' => 'Irmao, neto ou bisneto sem arrimo ate 21 anos',
        '24' => 'Irmao, neto ou bisneto universitario sem arrimo ate 24 anos',
        '25' => 'Pais, avos e bisavos',
        '26' => 'Menor pobre ate 21 anos',
        '31' => 'Pessoa absolutamente incapaz (tutor/curador)',
        '41' => 'Agregado/outro',
        '51' => 'Pessoa absolutamente incapaz',
    ];

    public string $valor;

    public function __construct(string $valor)
    {
        $valor = str_pad($valor, 2, '0', STR_PAD_LEFT);

        if (strlen($valor) !== 2) {
            throw new \InvalidArgumentException(
                "Codigo dependente deve ter 2 digitos: '{$valor}'"
            );
        }

        $this->valor = $valor;
    }

    public function obterDescricao(): string
    {
        return self::DESCRICOES[$this->valor] ?? "Codigo dependente {$this->valor}";
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
