<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cnpj;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 80 - Rendimento com Exigibilidade Suspensa - 123 caracteres.
 * Armazena rendimentos tributaveis cujo imposto esta com exigibilidade suspensa
 * (decisao judicial ou administrativa). Um registro por fonte pagadora.
 */
final readonly class RegistroExigibilidadeSuspensaDTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public Cnpj $cnpjFontePagadora,
        public string $nomeFontePagadora = '',
        public ValorMonetario $rendimentosTributaveis = new ValorMonetario(0),
        public ValorMonetario $depositosJudiciais = new ValorMonetario(0),
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::EXIGIBILIDADE_SUSPENSA;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('cnpjFontePagadora', 14, 14, TipoCampo::NUMERICO),
            new CampoDTO('nomeFontePagadora', 28, 60, TipoCampo::ALFA),
            new CampoDTO('rendimentosTributaveis', 88, 13, TipoCampo::NUMERICO, descricao: 'Rendimentos tributaveis com exigibilidade suspensa (centavos)'),
            new CampoDTO('depositosJudiciais', 101, 13, TipoCampo::NUMERICO, descricao: 'Depositos judiciais (centavos)'),
            new CampoDTO('checksum', 114, 10, TipoCampo::NUMERICO),
        );
    }
}
