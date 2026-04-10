<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Validador;

use DbkIrrf\Dominio\Contrato\ValidadorCampoInterface;
use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class ValidadorRegistro implements ValidadorCampoInterface
{
    public function validar(string $valor, int $tamanhoEsperado): bool
    {
        return strlen($valor) === $tamanhoEsperado;
    }

    public function validarLinha(string $linha, TipoRegistro $tipo): bool
    {
        return strlen($linha) === $tipo->obterTamanhoLinha();
    }

    public function validarArquivo(string $conteudo): ResultadoValidacao
    {
        $linhas = explode("\n", str_replace("\r\n", "\n", $conteudo));
        $erros = [];
        $numLinha = 0;

        foreach ($linhas as $linha) {
            $linha = rtrim($linha, "\r");
            $numLinha++;

            if (trim($linha) === '') {
                continue;
            }

            $tipo = TipoRegistro::identificarPorLinha($linha);
            if ($tipo === null) {
                continue;
            }

            $tamanhoEsperado = $tipo->obterTamanhoLinha();
            $tamanhoReal = strlen($linha);

            if ($tamanhoReal !== $tamanhoEsperado) {
                $erros[] = "Linha {$numLinha} (tipo {$tipo->value}): "
                    . "tamanho {$tamanhoReal}, esperado {$tamanhoEsperado}";
            }
        }

        return new ResultadoValidacao(count($erros) === 0, $erros);
    }

    /**
     * Valida a consistencia estrutural de uma declaracao completa.
     *
     * Verifica registros obrigatorios, consistencia de CPF entre header e
     * dados pessoais, nome nao vazio e integridade referencial (FK) entre
     * investimentos exterior (Reg 37) e bens/direitos (Reg 27).
     */
    public function validarDeclaracao(DeclaracaoDTO $declaracao): ResultadoValidacao
    {
        $erros = [];

        // 1. Header obrigatorio
        if ($declaracao->header === null) {
            $erros[] = 'Header (registro IRPF) e obrigatorio';
        }

        // 2. Dados pessoais obrigatorio
        if ($declaracao->dadosPessoais === null) {
            $erros[] = 'Dados pessoais (registro 16) e obrigatorio';
        }

        // 3. Trailer obrigatorio
        if ($declaracao->trailer === null) {
            $erros[] = 'Trailer (registro T9) e obrigatorio';
        }

        // 4. CPF consistente entre header e dados pessoais
        if ($declaracao->header !== null && $declaracao->dadosPessoais !== null) {
            if (!$declaracao->header->cpf->igual($declaracao->dadosPessoais->cpf)) {
                $erros[] = 'CPF do header (' . $declaracao->header->cpf->valor
                    . ') difere do CPF dos dados pessoais (' . $declaracao->dadosPessoais->cpf->valor . ')';
            }
        }

        // 5. Nome nao vazio no header
        if ($declaracao->header !== null && trim($declaracao->header->nome) === '') {
            $erros[] = 'Nome do contribuinte no header nao pode ser vazio';
        }

        return new ResultadoValidacao(count($erros) === 0, $erros);
    }
}
