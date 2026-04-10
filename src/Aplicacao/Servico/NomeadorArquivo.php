<?php

declare(strict_types=1);

namespace DbkIrrf\Aplicacao\Servico;

use DbkIrrf\Dominio\DTO\DeclaracaoDTO;
use DbkIrrf\Dominio\Enum\ModalidadeDeclaracao;
use DbkIrrf\Dominio\Enum\TipoDeclaracao;
use DbkIrrf\Dominio\ValorObjeto\Cpf;

final class NomeadorArquivo
{
    public function gerar(
        Cpf $cpf,
        int $anoExercicio,
        int $anoCalendario,
        TipoDeclaracao $tipo,
        ModalidadeDeclaracao $modalidade = ModalidadeDeclaracao::ANUAL,
    ): string {
        return sprintf(
            '%s-IRPF-%s-%d-%d-%s.DBK',
            $cpf->valor,
            $modalidade->value,
            $anoExercicio,
            $anoCalendario,
            $tipo->obterSufixoArquivo(),
        );
    }

    public function gerarDeDeclaracao(DeclaracaoDTO $declaracao): string
    {
        if ($declaracao->header === null) {
            throw new \RuntimeException('Header nao definido na declaracao');
        }

        $h = $declaracao->header;

        return $this->gerar($h->cpf, $h->anoExercicio, $h->anoCalendario, $h->tipoDeclaracao, $declaracao->modalidade);
    }
}
