<?php

declare(strict_types=1);

namespace DbkIrrf\Dominio\DTO;

use DbkIrrf\Dominio\Contrato\RegistroInterface;
use DbkIrrf\Dominio\Enum\TipoBeneficiario;
use DbkIrrf\Dominio\Enum\TipoCampo;
use DbkIrrf\Dominio\Enum\TipoRegistro;
use DbkIrrf\Dominio\ValorObjeto\Checksum;
use DbkIrrf\Dominio\ValorObjeto\Cpf;
use DbkIrrf\Dominio\ValorObjeto\ValorMonetario;

/**
 * Registro 86 - Rendimentos Isentos e Nao Tributaveis (Outros / cod 26) - 191 caracteres.
 * Variante do Registro 84 para o codigo 26 ("Outros"), que exige descricao livre obrigatoria.
 * Diferenca estrutural: campo descricaoLivre (pos 117-176, 60 chars) em vez de valorAdicional.
 */
final readonly class RegistroRendimentoIsento86DTO implements RegistroInterface
{
    public function __construct(
        public Cpf $cpf,
        public TipoBeneficiario $tipoBeneficiario = TipoBeneficiario::TITULAR,
        public Cpf $cpfBeneficiario = new Cpf('00000000000'),
        public string $codigoTipoRendimento = '0026',
        public string $cnpjFontePagadora = '',
        public string $nomeFontePagadora = '',
        public ValorMonetario $valorRendimentoIsento = new ValorMonetario(0),
        public string $descricaoLivre = '',
        public Checksum $checksum = new Checksum('0000000000'),
    ) {
    }

    public function obterTipo(): TipoRegistro
    {
        return TipoRegistro::RENDIMENTO_ISENTO_OUTROS;
    }

    public static function obterLayout(): LayoutRegistro
    {
        return new LayoutRegistro(
            new CampoDTO('tipoRegistro', 1, 2, TipoCampo::NUMERICO),
            new CampoDTO('cpf', 3, 11, TipoCampo::NUMERICO),
            new CampoDTO('tipoBeneficiario', 14, 1, TipoCampo::ALFA, descricao: 'T=titular, D=dependente'),
            new CampoDTO('cpfBeneficiario', 15, 11, TipoCampo::NUMERICO),
            new CampoDTO('codigoTipoRendimento', 26, 4, TipoCampo::NUMERICO, descricao: 'Codigo tipo rendimento (0026=Outros)'),
            new CampoDTO('cnpjFontePagadora', 30, 14, TipoCampo::ALFA),
            new CampoDTO('nomeFontePagadora', 44, 60, TipoCampo::ALFA),
            new CampoDTO('valorRendimentoIsento', 104, 13, TipoCampo::NUMERICO, descricao: 'Valor rendimento isento (centavos)'),
            new CampoDTO('descricaoLivre', 117, 60, TipoCampo::ALFA, descricao: 'Descricao livre obrigatoria para cod 26'),
            new CampoDTO('reservadoZeros', 177, 5, TipoCampo::NUMERICO, obrigatorio: false),
            new CampoDTO('checksum', 182, 10, TipoCampo::NUMERICO),
        );
    }
}
