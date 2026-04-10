<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\GeradorRegistroInterface;
use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;
use DbkIrrf\Infraestrutura\Formatador\FormatadorMonetario;
use DbkIrrf\Infraestrutura\Formatador\FormatadorNumerico;
use DbkIrrf\Infraestrutura\Formatador\FormatadorTexto;

abstract class GeradorRegistroBase implements GeradorRegistroInterface
{
    protected FormatadorTexto $formatadorTexto;
    protected FormatadorNumerico $formatadorNumerico;
    protected FormatadorMonetario $formatadorMonetario;

    public function __construct()
    {
        $this->formatadorTexto = new FormatadorTexto();
        $this->formatadorNumerico = new FormatadorNumerico();
        $this->formatadorMonetario = new FormatadorMonetario();
    }

    public function gerar(RegistroInterface $registro): string
    {
        $linha = $this->gerarCampos($registro);
        $checksum = $this->calcularChecksum($linha);
        $linhaCompleta = $linha . (string) $checksum;

        $tamanhoEsperado = $this->suportaTipo()->obterTamanhoLinha();
        $tamanhoReal = strlen($linhaCompleta);

        if ($tamanhoReal !== $tamanhoEsperado) {
            throw new \RuntimeException(
                "Linha do registro {$this->suportaTipo()->value} tem {$tamanhoReal} chars, "
                . "esperado {$tamanhoEsperado}"
            );
        }

        return $linhaCompleta;
    }

    abstract protected function gerarCampos(RegistroInterface $registro): string;

    protected function calcularChecksum(string $linhaSemChecksum): Checksum
    {
        return Checksum::placeholder();
    }

    protected function texto(string $valor, int $tamanho): string
    {
        return $this->formatadorTexto->formatar($valor, $tamanho);
    }

    protected function numero(int|string $valor, int $tamanho): string
    {
        if (is_int($valor)) {
            return $this->formatadorNumerico->formatarInteiro($valor, $tamanho);
        }

        return $this->formatadorNumerico->formatar($valor, $tamanho);
    }

    protected function monetario(ValorMonetario $valor, int $tamanho = 13): string
    {
        return $this->formatadorMonetario->formatarValor($valor, $tamanho);
    }

    protected function data(Data $data): string
    {
        return (string) $data;
    }

    protected function espacos(int $tamanho): string
    {
        return str_repeat(' ', $tamanho);
    }

    protected function zeros(int $tamanho): string
    {
        return str_repeat('0', $tamanho);
    }

    protected function rawOuEspacos(string $raw, int $tamanho): string
    {
        return strlen($raw) === $tamanho ? $raw : str_repeat(' ', $tamanho);
    }

    protected function rawOuZeros(string $raw, int $tamanho): string
    {
        return strlen($raw) === $tamanho ? $raw : str_repeat('0', $tamanho);
    }

    protected function cpfOuEspacos(?\DbkIrrf\Dominio\ValorObjeto\Cpf $cpf, int $tamanho = 11): string
    {
        return $cpf !== null ? $cpf->valor : $this->espacos($tamanho);
    }

    protected function cnpjOuZeros(?\DbkIrrf\Dominio\ValorObjeto\Cnpj $cnpj, int $tamanho = 14): string
    {
        return $cnpj !== null ? $cnpj->valor : $this->espacos($tamanho);
    }

    protected function dataOuEspacos(?Data $data, int $tamanho = 8): string
    {
        return $data !== null ? (string) $data : $this->espacos($tamanho);
    }
}
