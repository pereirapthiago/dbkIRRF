<?php

declare(strict_types=1);

namespace DbkIrrf\Infraestrutura\Gerador;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\DTO\RegistroBemDireitoDTO;
use DbkIrrf\Dominio\Enum\TipoRegistro;

final class GeradorBemDireito extends GeradorRegistroBase
{
    public function suportaTipo(): TipoRegistro
    {
        return TipoRegistro::BEM_DIREITO;
    }

    protected function gerarCampos(RegistroInterface $registro): string
    {
        if (!$registro instanceof RegistroBemDireitoDTO) {
            throw new \InvalidArgumentException('Esperado RegistroBemDireitoDTO');
        }

        $r = $registro;
        $l = RegistroBemDireitoDTO::obterLayout();

        $valores = [
            'tipoRegistro' => $this->numero('27', $l->campo('tipoRegistro')->tamanho),
            'cpf' => $r->cpf->valor,
            'codigoItem' => $this->numero($r->codigoItem, $l->campo('codigoItem')->tamanho),
            'flagExterior' => $this->numero($r->flagExterior, $l->campo('flagExterior')->tamanho),
            'pais' => $this->numero($r->pais, $l->campo('pais')->tamanho),
            'descricao' => $this->texto($r->descricao, $l->campo('descricao')->tamanho),
            'valorAnterior' => $this->monetario($r->valorAnterior, $l->campo('valorAnterior')->tamanho),
            'valorAtual' => $this->monetario($r->valorAtual, $l->campo('valorAtual')->tamanho),
            'logradouro' => $this->texto($r->logradouro, $l->campo('logradouro')->tamanho),
            'numero' => $this->texto($r->numero, $l->campo('numero')->tamanho),
            'complemento' => $this->texto($r->complemento, $l->campo('complemento')->tamanho),
            'bairro' => $this->texto($r->bairro, $l->campo('bairro')->tamanho),
            'cep' => $this->numero($r->cep, $l->campo('cep')->tamanho),
            'uf' => $this->texto($r->uf, $l->campo('uf')->tamanho),
            'codigoMunicipioIbge' => $this->numero($r->codigoMunicipioIbge, $l->campo('codigoMunicipioIbge')->tamanho),
            'municipio' => $this->texto($r->municipio, $l->campo('municipio')->tamanho),
            'camposAdicionaisRaw1' => $this->rawOuEspacos($r->camposAdicionaisRaw1, $l->campo('camposAdicionaisRaw1')->tamanho),
            'agencia' => $this->numero($r->agencia, $l->campo('agencia')->tamanho),
            'reservado867' => $this->rawOuEspacos($r->reservado867, $l->campo('reservado867')->tamanho),
            'dvConta' => $this->texto($r->dvConta, $l->campo('dvConta')->tamanho),
            'camposAdicionaisRaw2' => $this->rawOuEspacos($r->camposAdicionaisRaw2, $l->campo('camposAdicionaisRaw2')->tamanho),
            'dataAquisicao' => $this->data($r->dataAquisicao),
            'reservado905' => $this->rawOuEspacos($r->reservado905, $l->campo('reservado905')->tamanho),
            'renavam' => $this->numero($r->renavam, $l->campo('renavam')->tamanho),
            'numeroConta' => $this->numero($r->numeroConta, $l->campo('numeroConta')->tamanho),
            'camposAdicionaisRaw3' => $this->rawOuEspacos($r->camposAdicionaisRaw3, $l->campo('camposAdicionaisRaw3')->tamanho),
            'aplicFinancRendPerda' => $this->monetario($r->aplicFinancRendPerda, $l->campo('aplicFinancRendPerda')->tamanho),
            'camposAdicionaisRaw4' => $this->rawOuEspacos($r->camposAdicionaisRaw4, $l->campo('camposAdicionaisRaw4')->tamanho),
            'codigoGrupo' => $this->numero($r->codigoGrupo, $l->campo('codigoGrupo')->tamanho),
            'camposAdicionaisRaw5a' => $this->rawOuEspacos($r->camposAdicionaisRaw5a, $l->campo('camposAdicionaisRaw5a')->tamanho),
            'aplicFinancRendPerdaAlt' => $this->monetario($r->aplicFinancRendPerdaAlt, $l->campo('aplicFinancRendPerdaAlt')->tamanho),
            'aplicFinancImpExteriorAlt' => $this->monetario($r->aplicFinancImpExteriorAlt, $l->campo('aplicFinancImpExteriorAlt')->tamanho),
            'lucrosDivValorRecebido' => $this->monetario($r->lucrosDivValorRecebido, $l->campo('lucrosDivValorRecebido')->tamanho),
            'lucrosDivImpostoPago' => $this->monetario($r->lucrosDivImpostoPago, $l->campo('lucrosDivImpostoPago')->tamanho),
            'camposAdicionaisRaw5b' => $this->rawOuEspacos($r->camposAdicionaisRaw5b, $l->campo('camposAdicionaisRaw5b')->tamanho),
        ];

        // Posicoes 1023-1112: layout condicional por grupo
        // Grupo 06 (Depositos a vista): agencia/DV/CNPJ/BACEN/CPF/conta em posicoes especificas
        // Grupo 07 / cod 99 (Fundos exterior): usa aplicFinancImpExterior (pos 1039-1051)
        // Demais grupos: usa cnpj (pos 1042-1055)
        if ($r->codigoGrupo === '06') {
            $valores['agencia06'] = $this->numero($r->agencia06, $l->campo('agencia06')->tamanho);
            $valores['dvConta06'] = $r->dvConta06;
            $valores['separador06'] = ' ';
            $valores['cnpjBanco06'] = $r->cnpjBanco06 === ''
                ? str_repeat('0', $l->campo('cnpjBanco06')->tamanho)
                : $this->numero($r->cnpjBanco06, $l->campo('cnpjBanco06')->tamanho);
            $valores['codBacen06'] = $this->numero($r->codBacen06, $l->campo('codBacen06')->tamanho);
            $valores['separadorT06'] = 'T';
            $valores['cpfTitular06'] = $this->numero($r->cpfTitular06, $l->campo('cpfTitular06')->tamanho);
            $valores['numeroConta06'] = $this->texto($r->numeroConta06, $l->campo('numeroConta06')->tamanho);
        } elseif ($r->codigoGrupo === '07' && $r->codigoItem === '99') {
            $valores['aplicFinancImpExterior'] = $this->monetario($r->aplicFinancImpExterior, $l->campo('aplicFinancImpExterior')->tamanho);
        } else {
            $valores['cnpj'] = $r->cnpj === ''
                ? str_repeat(' ', $l->campo('cnpj')->tamanho)
                : $this->numero($r->cnpj, $l->campo('cnpj')->tamanho);
        }

        return $l->montarLinha($valores);
    }
}
