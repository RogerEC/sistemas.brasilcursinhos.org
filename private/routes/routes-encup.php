<?php
// Arquivo que armazena todas as rotas do site
use Router\Route as Route;
use App\Page;

Route::get('/', 'Encup@showHomePage');

Route::get(['set' => '/error/{code}', 'as' => 'pageError'], function($code){
    Page::showErrorHttpPage($code);
});

Route::get('/fotos', 'Encup@showPhotosPage');