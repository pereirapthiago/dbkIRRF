<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Leitor;

use DbkIrrf\Dominio\Contrato\LeitorRegistroInterface;
use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

abstract class LeitorRegistroBase implements LeitorRegistroInterface
{
    public function ler(string $linha): RegistroInterface
    {
        $tamanhoEsperado = $this->suportaTipo()->obterTamanhoLinha();
        $tamanhoReal = strlen($linha);

        if ($tamanhoReal !== $tamanhoEsperado) {
            throw new \RuntimeException(
                "Linha do registro {$this->suportaTipo()->value} tem {$tamanhoReal} chars, "
                . "esperado {$tamanhoEsperado}"
            );
        }

        return $this->lerCampos($linha);
    }

    abstract protected function lerCampos(string $linha): RegistroInterface;

    /**
     * Extrai substring da linha usando posicoes 1-based (conforme documentacao DBK).
     */
    protected function extrair(string $linha, int $posicaoInicial, int $tamanho): string
    {
        return substr($linha, $posicaoInicial - 1, $tamanho);
    }

    protected function extrairTexto(string $linha, int $posicaoInicial, int $tamanho): string
    {
        return rtrim($this->extrair($linha, $posicaoInicial, $tamanho));
    }

    protected function extrairNumero(string $linha, int $posicaoInicial, int $tamanho): int
    {
        $valor = $this->extrair($linha, $posicaoInicial, $tamanho);
        $valor = ltrim($valor, '0') ?: '0';

        return (int) $valor;
    }

    protected function extrairNumeroTexto(string $linha, int $posicaoInicial, int $tamanho): string
    {
        return $this->extrair($linha, $posicaoInicial, $tamanho);
    }

    protected function extrairCpf(string $linha, int $posicaoInicial): Cpf
    {
        return new Cpf($this->extrair($linha, $posicaoInicial, 11));
    }

    protected function extrairCnpj(string $linha, int $posicaoInicial): Cnpj
    {
        return new Cnpj($this->extrair($linha, $posicaoInicial, 14));
    }

    protected function extrairData(string $linha, int $posicaoInicial): Data
    {
        $valor = $this->extrair($linha, $posicaoInicial, 8);

        if (trim($valor) === '') {
            return Data::espacosVazios();
        }

        return new Data($valor);
    }

    protected function extrairMonetario(string $linha, int $posicaoInicial, int $tamanho = 13): ValorMonetario
    {
        $valor = $this->extrair($linha, $posicaoInicial, $tamanho);

        return ValorMonetario::deString($valor);
    }

    protected function extrairChecksum(string $linha): Checksum
    {
        return Checksum::deLinha($linha);
    }
}
