<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\CodigoDependente;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;

/**
 * Registro 25 - Dependentes - 224 caracteres.
 */
final readonly class RegistroDependenteDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public int $sequencial = 1,
        public string $tipoDependente = '',
        public string $nomeDependente = '',
        public Data $dataNascimento = new Data('00000000'),
        public ?Cpf $cpfDependente = null,
        public string $camposAdicionaisRaw = '            ',
        public bool $moraTitular = false,
        public string $email = '',
        public string $ddd = '',
        public string $celular = '',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::DEPENDENTE;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('sequencial', 14, 5, TipoCampo::NUMERICO),
            new CampoDTO('tipoDependente', 19, 2, TipoCampo::NUMERICO, descricao: 'Tipo dependente (21=filho, 11=companheiro, etc)'),
            new CampoDTO('nomeDependente', 21, 60, TipoCampo::ALFA),
            new CampoDTO('dataNascimento', 81, 8, TipoCampo::DATA),
            new CampoDTO('cpfDependente', 89, 11, TipoCampo::NUMERICO),
            new CampoDTO('desconhecido', 100, 12, TipoCampo::ALFA, obrigatorio: false, descricao: 'Desconhecido'),
            new CampoDTO('moraTitular', 112, 1, TipoCampo::NUMERICO, descricao: '0=nao, 1=sim'),
            new CampoDTO('email', 113, 90, TipoCampo::ALFA, obrigatorio: false),
            new CampoDTO('ddd', 203, 2, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('celular', 205, 9, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('tipoTelefone', 214, 1, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('checksum', 215, 10, TipoCampo::NUMERICO),
        );
    }
}
