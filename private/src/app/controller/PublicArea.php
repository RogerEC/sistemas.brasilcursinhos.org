<?php 
// Controlador para área pública
namespace App\Controller;

use App\Page;

class PublicArea {
    
    // exibe a página inicial do site
    public function showHomePage()
    {   
        Page::render('@public/homepage.html');
    }
}