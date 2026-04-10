<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 88 - Rendimentos Sujeitos a Tributacao Exclusiva/Definitiva - 131 caracteres.
 * Estrutura identica ao Registro 84 (sem campo valorAdicional de 13 chars).
 */
final readonly class RegistroTribExclusivaDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public TipoBeneficiario $tipoBeneficiario = TipoBeneficiario::TITULAR,
        public Cpf $cpfBeneficiario = new Cpf('00000000000'),
        public string $codigoTipoRendimento = '0006',
        public Cnpj $cnpjFontePagadora = new Cnpj('00000000000000'),
        public string $nomeFontePagadora = '',
        public ValorMonetario $valorRendimento = new ValorMonetario(0),
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::TRIBUTACAO_EXCLUSIVA;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('tipoBeneficiario', 14, 1, TipoCampo::ALFA),
            new CampoDTO('cpfBeneficiario', 15, 11, TipoCampo::NUMERICO),
            new CampoDTO('codigoTipoRendimento', 26, 4, TipoCampo::NUMERICO, descricao: 'Codigo tipo rendimento (0006=Aplicacoes financeiras)'),
            new CampoDTO('cnpjFontePagadora', 30, 14, TipoCampo::NUMERICO),
            new CampoDTO('nomeFontePagadora', 44, 60, TipoCampo::ALFA),
            new CampoDTO('valorRendimento', 104, 13, TipoCampo::NUMERICO, descricao: 'Valor rendimento (centavos)'),
            new CampoDTO('reservadoZeros', 117, 5, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('checksum', 122, 10, TipoCampo::NUMERICO),
        );
    }
}
