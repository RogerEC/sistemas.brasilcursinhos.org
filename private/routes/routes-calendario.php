<?php
// Arquivo que armazena todas as rotas do site
use Router\Route as Route;
use App\Page;

Route::get('/', 'Calendar@showHomePage');

Route::get(['set' => '/error/{code}', 'as' => 'pageError'], function($code){
    Page::showErrorHttpPage($code);
});