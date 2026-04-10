<?php

require __DIR__ . '/vendor/autoload.php';

use DbkIrrf\Dominio\DTO\RegistroHeaderDTO;
use DbkIrrf\Dominio\DTO\RegistroDadosPessoaisDTO;

$nosso = file_get_contents(__DIR__ . '/71930207140-IRPF-A-2026-2025-ORIGI.DBK');
$valido = file_get_contents(__DIR__ . '/71930207140-IRPF-A-2026-2025-ORIGI-VALIDO.DBK');

$nossoLinhas = preg_split('/\r?\n/', trim($nosso));
$validoLinhas = preg_split('/\r?\n/', trim($valido));

function compararRegistro(string $titulo, array $campos, string $linhaNosso, string $linhaValido): void
{
    echo "=== {$titulo} ===\n";
    echo "Nosso tamanho: " . strlen($linhaNosso) . " | Valido tamanho: " . strlen($linhaValido) . "\n\n";

    foreach ($campos as $campo) {
        $pos = $campo->posicaoInicial - 1;
        $tam = $campo->tamanho;

        $vNosso = substr($linhaNosso, $pos, $tam);
        $vValido = substr($linhaValido, $pos, $tam);

        if ($vNosso !== $vValido) {
            $label = str_pad($campo->nome, 30);
            $posStr = "pos " . str_pad((string)$campo->posicaoInicial, 4) . " tam " . str_pad((string)$tam, 3);
            echo "DIFF {$label} {$posStr} | NOSSO=[{$vNosso}] VALIDO=[{$vValido}]\n";
        }
    }
    echo "\n";
}

// Header (linha 0)
$layoutHeader = RegistroHeaderDTO::obterLayout();
compararRegistro('HEADER (IRPF)', $layoutHeader->obterTodos(), $nossoLinhas[0], $validoLinhas[0]);

// Dados Pessoais (linha 1)
$layoutDP = RegistroDadosPessoaisDTO::obterLayout();
compararRegistro('DADOS PESSOAIS (Reg 16)', $layoutDP->obterTodos(), $nossoLinhas[1], $validoLinhas[1]);
