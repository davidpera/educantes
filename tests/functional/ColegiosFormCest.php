<?php
/**
 * Test para la pagina de Noticias
 */
class ColegiosFormCest
{
    /**
     * Comprueba que no puedes gestionar colegios sin logearte
     * @param FunctionalTester $I [description]
     */
    public function ColegiosGestionarSinLoguearse(\FunctionalTester $I)
    {
        $I->amOnRoute('colegios/gestionar');
        $I->amOnRoute('site/index');
    }

    /**
     * Comprueba que puede añadir un colegio
     * @param FunctionalTester $I [description]
     */
    public function ColegiosGestionarDatosBien(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\app\models\Usuarios::findOne(['nom_usuario' => 'pepe']));
        $I->amOnRoute('colegios/gestionar');
        $I->submitForm('#gestionar-form', [
            'LoginForm[cif]' => 'Z99999999',
            'LoginForm[nombre]' => 'Educantes Marruecos',
            'LoginForm[email]' => 'educantes.marruecos@educantes.com',
            'LoginForm[cod_postal]' => '11540',
            'LoginForm[direccion]' => 'c/123',
        ]);
        $I->see('Gestion de Colegios', 'h2');
    }

    /**
     * Comprueba que puede añadir un colegio
     * @param FunctionalTester $I [description]
     */
    public function ColegiosGestionarDatosMal(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\app\models\Usuarios::findOne(['nom_usuario' => 'pepe']));
        $I->amOnRoute('colegios/gestionar');
        $I->submitForm('#gestionar-form', [
            'LoginForm[cif]' => '',
            'LoginForm[nombre]' => 'Educantes Marruecos',
            'LoginForm[email]' => 'educantes.marruecos@educantes.com',
            'LoginForm[cod_postal]' => '11540',
            'LoginForm[direccion]' => 'c/123',
        ]);
        $I->see('Cif no puede estar vacío.', 'div');
    }
}
