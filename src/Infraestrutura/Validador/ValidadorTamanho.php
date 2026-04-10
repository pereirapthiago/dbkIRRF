<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Validador;

use DbkIrrf\Dominio\Contrato\ValidadorCampoInterface;
use DbkIrrf\Dominio\DTO\CampoDTO;

/**
 * Valida tamanho de campos individuais e conjuntos de campos.
 */
final class ValidadorTamanho implements ValidadorCampoInterface
{
    public function validar(string $valor, int $tamanhoEsperado): bool
    {
        return strlen($valor) === $tamanhoEsperado;
    }

    public function validarCampo(CampoDTO $campo, string $valor): bool
    {
        return strlen($valor) === $campo->tamanho;
    }

    /**
     * Valida se um campo numerico contem apenas digitos.
     */
    public function validarNumerico(string $valor): bool
    {
        return ctype_digit($valor);
    }

    /**
     * Valida se um campo monetario tem o formato correto (N digitos numericos).
     */
    public function validarMonetario(string $valor, int $tamanho = 13): bool
    {
        return strlen($valor) === $tamanho && ctype_digit($valor);
    }

    /**
     * Valida se uma data tem formato ddmmaaaa (8 digitos).
     */
    public function validarData(string $valor): bool
    {
        if (strlen($valor) !== 8) {
            return false;
        }

        if ($valor === '00000000' || trim($valor) === '') {
            return true;
        }

        return ctype_digit($valor);
    }
}
