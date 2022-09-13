<?php 
// Controlador para área pública
namespace App\Controller;

use App\Page;

class Calendar {
    
    // exibe a página inicial do site
    public function showHomePage()
    {   
        Page::render('@calendario/homepage.html');
    }
}