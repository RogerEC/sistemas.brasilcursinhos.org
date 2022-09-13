<?php 
// Controlador para área pública
namespace App\Controller;

use App\Page;

class Encup {
    
    // exibe a página inicial do site
    public function showHomePage()
    {   
        Page::render('@encup/homepage.html');
    }
}