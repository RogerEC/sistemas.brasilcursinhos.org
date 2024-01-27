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

Route::get('/administrador/voting/{idvoting}', 'Administrator@showVotingPage');

Route::post('/administrador/voting/{idvoting}', 'Administrator@getVoters');

Route::get('/voting/{code}', 'PublicArea@showPresenceVotingPage');

Route::post('/voting/{code}', 'PublicArea@savePresenceVoting');

Route::post('/activities/delete', 'Event@deleteActivity');

Route::post('/activities/update', 'Event@updateActivity');

Route::post('/activities/insert', 'Event@insertActivity');

Route::post('/participants/delete', 'Event@deleteParticipant');

Route::post('/participants/update', 'Event@updateParticipant');

Route::post('/participants/insert', 'Event@insertParticipant');

Route::post('/users/delete', 'Administrator@deleteUser');

Route::post('/users/update', 'Administrator@updateUser');

Route::post('/users/insert', 'Administrator@insertUser');

Route::post('/verificar-cpf-participante', 'Event@checkParticipantCpf');

Route::post('/app/login', 'Application@checkLogin');

Route::post('/presence/check', 'Event@checkPresence');

Route::post('/presence/confirm', 'Event@confirmPresence');

Route::post('/presence/cancel', 'Event@cancelPresence');

Route::post('/usuario/participant', 'Event@checkParticipant');

Route::post('/administrador/participant', 'Event@checkParticipant');

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

