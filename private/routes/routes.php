<?php
// Arquivo que armazena todas as rotas do site
use Router\Route as Route;
use App\Page;

Route::get('/', 'PublicArea@showHomePage');

Route::get(['set' => '/error/{code}', 'as' => 'pageError'], function($code){
    Page::showErrorHttpPage($code);
});

Route::get('/login', 'Access@showLoginPage');

Route::post('/login', 'Access@checkLogin');

Route::post('/logout', 'Access@makeLogout');

Route::get('/recuperar-senha', 'Access@showRecoverPasswordPage');

Route::post('/recuperar-senha', 'Access@sendEmailVerificationCode');

Route::get('/verificar/{user}/{code}', 'Access@showValidationPage');

Route::get('/usuario', 'User@showUserPage');

Route::get('/administrador', 'Administrator@showAdministratorPage');

Route::get('/usuario/{subpage}', 'User@showUserSubpage');

Route::get('/administrador/{subpage}', 'Administrator@showAdministratorSubpage');

if(DEBUG_MODE) {
    Route::get('/teste', 'Teste@showTeste');

    Route::get('/teste2', 'Teste@showTeste2');

    Route::get('/info', 'Teste@showInfoPage');

    Route::get('/oauth', function(){
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/get_oauth_token.php';
        exit;
    });
    
    Route::post('/oauth', function(){
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/get_oauth_token.php';
        exit;
    });
}

