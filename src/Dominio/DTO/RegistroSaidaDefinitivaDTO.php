<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\Data;

/**
 * Registro 39 - Saida Definitiva do Pais - 193 caracteres.
 * Aparece apenas em declaracoes do tipo IRPF-S.
 */
final readonly class RegistroSaidaDefinitivaDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public ?Cpf $cpfProcurador = null,
        public string $nomeProcurador = '',
        public string $enderecoProcurador = '',
        public Data $dataNaoResidente = new Data('00000000'),
        public Data $dataResidentePais = new Data('00000000'),
        public string $codigoPaisDestino = '000',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::SAIDA_DEFINITIVA;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('cpfProcurador', 14, 11, TipoCampo::NUMERICO),
            new CampoDTO('nomeProcurador', 25, 60, TipoCampo::ALFA),
            new CampoDTO('enderecoProcurador', 85, 80, TipoCampo::ALFA),
            new CampoDTO('dataNaoResidente', 165, 8, TipoCampo::DATA, descricao: 'Data em que deixou de ser residente (ddmmaaaa)'),
            new CampoDTO('dataResidentePais', 173, 8, TipoCampo::DATA, descricao: 'Data em que se tornou residente no pais destino (ddmmaaaa)'),
            new CampoDTO('codigoPaisDestino', 181, 3, TipoCampo::NUMERICO, descricao: 'Codigo do pais de destino'),
            new CampoDTO('checksum', 184, 10, TipoCampo::NUMERICO),
        );
    }
}
